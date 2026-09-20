<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $planId = $this->route('id') ?? $this->route('plan');

        return [
            'name'          => 'sometimes|required|string|max:255',
            'code'          => ['sometimes', 'required', 'string', 'max:50', Rule::unique('plans', 'code')->ignore($planId)],
            'description'   => 'nullable|string',
            'price'         => 'sometimes|required|numeric|min:0',
            'duration_days' => 'sometimes|required|integer|min:1',
            'max_sections'  => 'nullable|integer|min:0',
            'max_messages'  => 'nullable|integer|min:0',
            'max_exports'   => 'nullable|integer|min:0',
            'billing_cycle' => 'nullable|string|max:50',
            'is_active'     => 'sometimes|boolean'
        ];
    }
}