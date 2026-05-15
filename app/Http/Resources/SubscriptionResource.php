<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'subscription_id' => $this->id,

            'plan' => new PlanResource($this->plan),

            //'answer' => $this->answer ?? null,
        ];
    }
}