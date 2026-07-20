<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function webhook(Request $request)
    {
        $paymentId = $request->input('data.id');

        $payment = $this->mercadoPagoClient->get($paymentId);

        Log::info('Webhook Mercado Pago', [
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
        ]);


        if ($payment->status === 'approved') {

            DB::transaction(function () use ($payment) {

                $localPayment = Payments::where(
                    'provider_payment_id',
                    $payment->id
                )->first();

                if (!$localPayment) {
                    return;
                }

                // Actualizar pago
                $localPayment->update([
                    'status' => 'completed'
                ]);


                // Crear suscripción
                $subscription = $this->conversationService
                    ->registerUserSuscription(
                        $localPayment->user_id,
                        $localPayment->package_id
                    );


                // Crear conversaciones...
            });
        }


        return response()->json(['received' => true]);
    }
}
