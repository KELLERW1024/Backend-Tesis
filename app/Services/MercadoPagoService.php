<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;

class MercadoPagoService
{
    public static function init()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }
}