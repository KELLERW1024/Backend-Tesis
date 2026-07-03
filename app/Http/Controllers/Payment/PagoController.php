<?php

namespace App\Http\Controllers\Payment;
use App\Models\Coupon;
use App\Models\Payments;
use MercadoPago\MercadoPagoConfig;
use App\Services\MercadoPagoService;
use MercadoPago\Client\Payment\PaymentClient;

use Illuminate\Support\Facades\DB;

use App\Services\ConversationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MercadoPago\Client\Common\RequestOptions;
use App\Models\Plan;
use App\Models\CouponRedemption;
use Illuminate\Support\Facades\Log;
use App\Models\Conversation;



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

         // Evita pagos duplicados si el usuario reintenta la solicitud
        // $request_options->setCustomHeaders([
        //     'X-Idempotency-Key' => (string) Str::uuid(),
        // ]);

        Log::info($request->all());

        try {

            $data = $request->input('formData');

            $couponCode = $request->coupon_code;

             // Verificamos que el plan exista 
            $plan = Plan::findOrFail($request->plan_id);

            $discountAmount = $request->discount_amount ?? 0 ;
            $originalPrice = $request->original_price;
            $finalAmount = $request->final_amount  ?? $plan->price;
            $planId = $request->plan_id;

            // 🔹 Cupón (opcional)
            $coupon = null;

            if ( $couponCode ) {
                $coupon = Coupon::where('code', $request->coupon_code)->firstOrFail();

                // 🚨 Evitar reutilización del cupón
                $alreadyUsed = CouponRedemption::where('coupon_id', $coupon->id)
                    ->where('user_id', auth()->id())
                    ->exists();

                if ($alreadyUsed) {
                    throw new \Exception('Ya has usado este cupón');
                }
            }

            // 🔹 Conversación + suscripción
            $conversationResult = $this->conversationService->startConversation(
                auth()->id(),
                $plan->id
            );

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
            ], $request_options);

            // Mapear estados de Mercado Pago a tu BD
            $status = match ($payment->status) {
                'approved' => 'completed',
                'pending', 'in_process' => 'pending',
                'rejected', 'cancelled' => 'failed',
                'refunded', 'charged_back' => 'refunded',
                default => 'pending',
            };
             
            Payments::create([
                'user_id' => auth()->id(), 
                'subscription_id' => $conversationResult['subscription_id'], // <-- ajusta según tu lógica
                'amount' => $payment->transaction_amount, // revisar
                'currency' => $payment->currency_id, // Revisar 
                'payment_provider' => 'mercadopago',
                'provider_payment_id' => $payment->id,
                'status' => $status,
                'payment_type' => 'subscription', // Pendiente
                'coupon_id' => $coupon?->id ,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
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

             return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'amount' => $payment->transaction_amount,
            ]);
        }

        catch (\MercadoPago\Exceptions\MPApiException $e) {

            return response()->json([
                'success' => false,
                'status' => $e->getApiResponse()->getStatusCode(),
                'response' => $e->getApiResponse()->getContent()
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
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

    // VER todos los pagos realizados 
    public function getPayments()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $conversations = Conversation::with([
            'user',
            'subscription.plan',
            'subscription.payments',
            'sectionProgress.section'
        ])->get();

        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],

            'conversations' => $conversations->map(function ($conversation) {

                return [
                    'id' => $conversation->id,

                    'user' => [
                        'id' => $conversation->user?->id,
                        'name' => $conversation->user?->name,
                    ],

                    'status' => $conversation->status,
                    'title' => $conversation->title,

                    'plan_name' => $conversation->subscription?->plan?->name,

                    'payments' => $conversation->subscription?->payments,
                ];
            }),
        ];

        return response()->json($data);
    }
}
