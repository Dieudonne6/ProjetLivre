<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLivreRequest extends FormRequest
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
        return [
            'nomL' => 'required',
            'categorieL' => 'required',
            'description' => 'required',
            'path' => 'required',
            'statut' => 'required',
            'date' => 'required',
            'prixL' => 'required'
        ];
    }

    public function failedValidator(Validator $validator)
    {

        throw new HttpResponseException(response()->json([
            'success' => 'false',
            'error' => 'true',
            'message' => 'Erreur de validation',
            'errorsLists' => $validator->errors()
        ]));
    }

    public function messages()
    {
        return[
            'nomL.required' => 'Ajouter le nom du livre',
            'categorieL.required' => 'Signifier la catégorie du livre',
            'description.required' => 'Une description du type du livre serait bien',
            'path.required' => 'Le lien du livre',
            'statut.required' => 'Gratuit ou premium?',
            'date.required' => 'Date dajout',
            'prixL.required' => 'Fixer un prix'
        ];
    }
}
