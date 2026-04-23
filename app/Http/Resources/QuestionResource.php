<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'text' => $this->question_text,
            'type' => $this->question_type,
            'order' => $this->order_index,
        ];
    }
}
