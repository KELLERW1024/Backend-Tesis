<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
     public function handle(Request $request)
    {
        // Guardar todo lo que llega (debug inicial)
        file_put_contents(
            storage_path('logs/mp_webhook.log'),
            json_encode($request->all()) . PHP_EOL,
            FILE_APPEND
        );

        return response()->json(["ok" => true]);
    }
}
