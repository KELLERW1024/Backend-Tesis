<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => 'sometimes|required|string|max:255',
            'description'         => 'nullable|string',
            'duration_months'     => 'sometimes|required|integer|min:1',
            'num_plans'           => 'sometimes|required|integer|min:1',
            'local_price'         => 'sometimes|required|numeric|min:0',
            'international_price' => 'nullable|numeric|min:0',
            'unit_price'          => 'nullable|numeric|min:0',
            'benefits'            => 'nullable|string',
            'is_active'           => 'sometimes|boolean',
            'plans'               => 'nullable|array',
            'plans.*'             => 'integer|exists:plans,id'
        ];
    }
}