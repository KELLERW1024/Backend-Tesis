<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'order' => $this->order_index,
            'description' => $this->description,

            // 👇 relación optimizada
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'progress' => $this->progress ?? null,
        ];
    }
}
