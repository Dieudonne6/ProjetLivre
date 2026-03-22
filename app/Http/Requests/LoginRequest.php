<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:155',
            'password' => 'required|string|min:6'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email must not exceed 155 characters.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 6 characters.',
        ];
    }
}
