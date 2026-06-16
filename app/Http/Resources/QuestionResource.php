<?php

namespace App\Http\Resources;

use App\Models\UserAnswers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'text' => $this->question_text,
            'detail' => $this->question_detail,
            'evidence' => $this->evidencia_detail,
            'validation' => $this->validation_detail,
            'apa' => $this->apa_detail,
            'type' => $this->question_type,
            'image' => $this -> allow_image,
            //'order' => $this->order_index,

            'answer_question' => $this->answer ?? null,
            'files' => $this->whenNotNull($this->files),
            'tables' => $this->whenNotNull($this->tables),
        ];
    }

}
