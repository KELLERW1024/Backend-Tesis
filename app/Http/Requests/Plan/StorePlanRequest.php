<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:50|unique:plans,code',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_sections'  => 'nullable|integer|min:0',
            'max_messages'  => 'nullable|integer|min:0',
            'max_exports'   => 'nullable|integer|min:0',
            'billing_cycle' => 'nullable|string|max:50',
            'is_active'     => 'nullable|boolean'
        ];
    }
}