<?php

namespace App\Http\Controllers\Payment;
use App\Models\Coupon;
use App\Models\Payments;
use MercadoPago\MercadoPagoConfig;
use App\Services\MercadoPagoService;
use MercadoPago\Client\Payment\PaymentClient;

use App\Services\ConversationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MercadoPago\Client\Common\RequestOptions;
use App\Models\Plan;
use App\Models\CouponRedemption;


class PagoController extends Controller
{

    private ConversationService $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }
    public function payment(Request $request)
    {
        MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));

        $client = new PaymentClient();

        $request_options = new RequestOptions();

        // $request_options->setCustomHeaders([
        //     "X-Idempotency-Key" => uniqid()
        // ]);

        try {

           $data = $request->input('formData');

            $payment = $client->create([
                // "body" => [
                    "token" => $data['token'],
                    "issuer_id" => $data['issuer_id'],
                    "payment_method_id" => $data['payment_method_id'],
                    "transaction_amount" => (float) $data['transaction_amount'],
                    "installments" => $data['installments'] ?? 1,
                    "payer" => [
                        "email" => data_get($data, 'payer.email'),
                        "identification" => [
                            "type" => data_get($data, 'payer.identification.type'),
                            "number" => data_get($data, 'payer.identification.number'),
                        ]
                    ]
                // ]
            ]);

            return response()->json($payment);
        }

        catch (\MercadoPago\Exceptions\MPApiException $e) {

                return response()->json([
                    'status' => $e->getApiResponse()->getStatusCode(),
                    'response' => $e->getApiResponse()->getContent()
                ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function registerYapePayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'security_code' => 'required|string',
            'operation_number' => 'required|unique:payments,operation_number',
            'voucher' => 'nullable|image|max:5120',

            'coupon_code'     => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'final_amount'    => 'nullable|numeric|min:0',
        ]);

        try {

            $result = DB::transaction(function () use ($request) {

                // 🔹 Plan
                $plan = Plan::findOrFail($request->plan_id);

                // 🔹 Cupón (opcional)
                $coupon = null;

                if ($request->coupon_code) {
                    $coupon = Coupon::where('code', $request->coupon_code)->firstOrFail();

                    // 🚨 Evitar reutilización del cupón
                    $alreadyUsed = CouponRedemption::where('coupon_id', $coupon->id)
                        ->where('user_id', auth()->id())
                        ->exists();

                    if ($alreadyUsed) {
                        throw new \Exception('Ya has usado este cupón');
                    }
                }

                // 🔹 Voucher
                $voucherPath = null;

                if ($request->hasFile('voucher')) {
                    $voucherPath = $request->file('voucher')
                        ->store('payments/yape', 'public');
                }

                // 🔹 Conversación + suscripción
                $conversationResult = $this->conversationService->startConversation(
                    auth()->id(),
                    $plan->id
                );

                // 🔹 Montos
                $originalAmount = $plan->price;
                $discountAmount = $request->discount_amount ?? 0;
                $finalAmount = $request->final_amount ?? $plan->price;

                // 🔹 Pago
                $payment = Payments::create([
                    'user_id' => auth()->id(),
                    'plan_id' => $plan->id,
                    'coupon_id' => $coupon?->id,
                    'security_code' => $request->security_code,
                    'operation_number' => $request->operation_number,
                    'voucher_path' => $voucherPath,

                    'amount' => $originalAmount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,

                    'payment_provider' => 'yape',
                    'status' => 'pending',
                    'subscription_id' => $conversationResult['subscription_id'],
                ]);

                // 🔹 Redención de cupón
                if ($coupon) {
                    CouponRedemption::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => auth()->id(),
                        'payment_id' => $payment->id,
                        'discount_amount' => $discountAmount,
                        'used_at' => now(),
                    ]);
                }

                return [
                    'payment_id' => $payment->id,
                    'conversation_id' => $conversationResult['conversation_id'],
                    'subscription_id' => $conversationResult['subscription_id'],
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado correctamente',
                'payment_id' => $result['payment_id'],
                'conversation_id' => $result['conversation_id'],
                'subscription_id' => $result['subscription_id']
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
