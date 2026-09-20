<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
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
        return [
            'name'      => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'email'     => 'required|string|email|max:150|unique:users,email',
            'password'  => 'required|string|min:6',
            'role_id'   => 'required|integer|exists:roles,id',
            'is_active' => 'boolean'
        ];
    }
}
