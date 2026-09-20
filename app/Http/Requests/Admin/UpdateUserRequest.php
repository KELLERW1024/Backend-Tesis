<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');
        return [
            'name'      => 'sometimes|required|string|max:150',
            'last_name' => 'sometimes|required|string|max:150',
            'email'     => 'sometimes|required|string|email|max:150|unique:users,email,' . $userId,
            'password'  => 'nullable|string|min:6',
            'role_id'   => 'sometimes|required|integer|exists:roles,id',
            'is_active' => 'sometimes|boolean'
        ];
    }
}
