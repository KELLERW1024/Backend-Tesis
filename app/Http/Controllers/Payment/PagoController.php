<?php

namespace App\Http\Controllers\Payment;
use MercadoPago\MercadoPagoConfig;
use App\Services\MercadoPagoService;
use MercadoPago\Client\Payment\PaymentClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MercadoPago\Client\Common\RequestOptions;

class PagoController extends Controller
{
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
}
