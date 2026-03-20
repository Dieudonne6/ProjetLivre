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
            'solde' => 'required|numeric|min:1'
        ];
    }

    public function messages()
    {
        return [
            'solde.required' => 'Le montant est obligatoire',
            'solde.numeric' => 'Le montant doit être un nombre',
            'solde.min' => 'Le montant doit être supérieur à 0'
        ];
    }
}