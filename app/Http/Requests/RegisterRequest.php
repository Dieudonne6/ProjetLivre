<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:155',
            'email' => 'required|email|unique:users,email|max:155',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'required|string|min:6',
            'telephone' => 'required|string|max:8',
            'statut' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name is required.',
            'name.max' => 'The name must not exceed 155 characters.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'email.max' => 'The email must not exceed 155 characters.',
            'image.required' => 'An image file is required.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'Supported image formats are: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image size must not exceed 2MB.',
            'password.required' => 'A password is required.',
            'password.min' => 'The password must be at least 6 characters.',
            'telephone.required' => 'The phone number is required.',
            'telephone.max' => 'The phone number must not exceed 8 characters.',
            'statut.required' => 'The status field is required.',
            'statut.integer' => 'The status must be an integer value.',
        ];
    }
}
