<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RechargeSoldeRequest extends FormRequest
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
            'solde' => 'required|numeric|min:10'
        ];
    }

    public function messages()
    {
        return [
            'solde.required' => 'The amount is required.',
            'solde.numeric' => 'The amount must be a numeric value.',
            'solde.min' => 'The amount must be at least 10.',
        ];
    }
}