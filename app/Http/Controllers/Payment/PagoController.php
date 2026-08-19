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
use App\Models\Package;
use App\Models\CouponRedemption;
use Illuminate\Support\Facades\Log;
use App\Models\Conversation;

use Illuminate\Support\Facades\Validator;



class PagoController extends Controller
{

    private ConversationService $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    public function payment(Request $request)
    {
        \Log::info($request->all());
        $request->merge([
            'plans' => array_values(array_filter($request->plans ?? []))
        ]);

        if (count($request->plans) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No hay planes seleccionados.'
            ], 200);
        }

        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'plans' => 'required|array|min:1',
            'plans.*' => 'exists:plans,id',

            'coupon_code'     => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'final_amount'    => 'nullable|numeric|min:0',
        ]);

       

         // Evita pagos duplicados si el usuario reintenta la solicitud
        // $request_options->setCustomHeaders([
        //     'X-Idempotency-Key' => (string) Str::uuid(),
        // ]);

        try {
            MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));

            $client = new PaymentClient();

            $request_options = new RequestOptions();

            $data = $request->input('formData');

            $couponCode = $request->coupon_code;

            // Verificamos que el paquete exista 
            $package= Package::findOrFail($request->package_id);

            $packageId = $request->package_id;

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
            // fin verificacion cupon 

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

            // Log del estado del pago =================================================
            Log::info('Mercado Pago - Estado del pago', [
                'payment_id' => $payment->id ?? null,
                'status' => $payment->status ?? null,
                'status_detail' => $payment->status_detail ?? null,
                'amount' => $payment->transaction_amount ?? null,
                'payment_method' => $payment->payment_method_id ?? null,
                'payer_email' => $payment->payer->email ?? null,
            ]);
            // =================================

            // Mapear estados de Mercado Pago a tu BD
            $status = match ($payment->status) {
                'approved' => 'completed',
                'pending', 'in_process' => 'pending',
                'rejected', 'cancelled' => 'failed',
                'refunded', 'charged_back' => 'refunded',
                default => 'pending',
            };


            $result = DB::transaction(function () use ($request, $payment, $status, $coupon, $package  ) {

                $discountAmount = $request->discount_amount ?? 0 ;
                // $originalPrice = $request->original_price;
                $finalAmount = $request->final_amount  ?? $package->local_price;

                    $suscriptionResult = null;
                    $conversationsIds = [];
               
                // Se crea la  Conversación + suscripción solo si el estado del pago es competado
                if ($status === 'completed' || $status === 'pending' ) {

                    $suscriptionResult = $this->conversationService->registerUserSuscription( auth()->id(), $request['package_id'] );


                    foreach ($request->plans as $planId) {
                            // 🔹 Conversación
                            $conversationResult = $this->conversationService->startConversation(
                                auth()->id(), 
                                $planId ,
                                $suscriptionResult['subscription_id'],
                            );

                            if (!$conversationResult) {
                                throw new \Exception('Error al crear la conversación para el plan ' . $planId);
                            }

                            $conversationsIds[] = $conversationResult['conversation_id'];
                    }

                     Payments::create([
                        'user_id' => auth()->id(), 
                        'subscription_id' => $suscriptionResult['subscription_id'], 
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

                    return [
                        'payment_id' => $payment->id,
                        'status' => $payment->status,
                        'status_detail' => $payment->status_detail,
                        'amount' => $payment->transaction_amount,
                        'subscription_id' => $suscriptionResult['subscription_id'],
                        'conversation_ids' => $conversationsIds,
                    ];
                }
             
               
            });

             return response()->json([
                'success' => true,
                'payment_id' => $result['payment_id'],
                'status' => $result['status'],
                'status_detail' => $result['status_detail'],
                'amount' => $result['amount'],
                'subscription_id' => $result['subscription_id'],
                'conversation_ids' => $result['conversation_ids'],
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
   
    public function registerFree(Request $request)
    {
        $request->validate([
            'package_id' => 'required|integer|exists:packages,id',
            'plans' => 'required|array|min:1',
            'plans.*' => 'integer|exists:plans,id',
           
        ]);

        try {

            $result = DB::transaction(function () use ($request) {

                $packageId = $request->package_id;
                $plans = $request->plans;

                $conversationsIds = [];

                $suscriptionResult = $this->conversationService->registerUserSuscription( auth()->id(), $packageId );
                //Pago
                $payment = Payments::create([
                    'user_id' => auth()->id(),
                    'amount' => 0,
                    'final_amount' => 0,
                    'payment_provider' => 'free',
                    'status' => 'completed',
                    'subscription_id' => $suscriptionResult['subscription_id'],
                ]);

                foreach ( $plans as $planId ) {

                    $conversationResult = $this->conversationService->startConversation(
                        auth()->id(), 
                        $planId ,
                        $suscriptionResult['subscription_id'],
                    );

                    if (!$conversationResult) {
                        throw new \Exception('Error al crear la conversación para el plan ' . $planId);
                    }

                    $conversationsIds[] = $conversationResult['conversation_id'];
                }

                return [
                    'payment_id' => $payment->id,
                    'subscription_id' => $suscriptionResult['subscription_id'],
                    'conversation_ids' => $conversationsIds,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado correctamente',
                'payment_id' => $result['payment_id'],
                'subscription_id' => $result['subscription_id'],
                'conversation_ids' => $result['conversation_ids']
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function registerYapePayment(Request $request)
    {
        \Log::info($request->all());

        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'plans' => 'required|array|min:1',
            'plans.*' => 'exists:plans,id',
            'security_code' => 'required|string',
            'operation_number' => 'required|unique:payments,operation_number',
            'voucher' => 'nullable|image|max:5120',

            'coupon_code'     => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'final_amount'    => 'nullable|numeric|min:0',
        ]);
        try {

            $result = DB::transaction(function () use ($request) {

                $packageId = $request->package_id;
                $plans = $request->plans;

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

                //

                $conversationsIds = [];

                $suscriptionResult = $this->conversationService->registerUserSuscription( auth()->id(), $packageId );
                
                

                // 🔹 Plan
                $package = Package::findOrFail($packageId);

                foreach ($request->plans as $planId) {

                    $userPlanResult = $this->conversationService->registerUserPlan( auth()->id(), $planId );
                    // 🔹 Conversación
                    $conversationResult = $this->conversationService->startConversation(
                        auth()->id(), 
                        $userPlanResult['plan_user_id'] ,
                        $suscriptionResult['subscription_id'],
                    );

                    if (!$conversationResult) {
                        throw new \Exception('Error al crear la conversación para el plan ' . $planId);
                    }

                    $conversationsIds[] = $conversationResult['conversation_id'];
                }

                

                // 🔹 Montos
                $originalAmount = $package->local_price;
                $discountAmount = $request->discount_amount ?? 0;
                $finalAmount = $request->final_amount ?? $package->local_price;

                // 🔹 Pago
                $payment = Payments::create([
                    'user_id' => auth()->id(),
                    'subscription_id' => $suscriptionResult['subscription_id'],
                    'coupon_id' => $coupon?->id,
                    'security_code' => $request->security_code,
                    'operation_number' => $request->operation_number,
                    'voucher_path' => $voucherPath,

                    'amount' => $originalAmount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,

                    'payment_provider' => 'yape',
                    'status' => 'pending',
                    
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
                    'subscription_id' => $suscriptionResult['subscription_id'],
                    'conversation_ids' => $conversationsIds,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado correctamente',
                'payment_id' => $result['payment_id'],
                'conversation_id' => $result['conversation_ids'],
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
            'userPlan.user',
            'subscription.plan',
            'subscription.payments',
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
                        'id' => $conversation->userPlan?->user?->id,
                        'name' => $conversation->userPlan?->user?->name,
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
