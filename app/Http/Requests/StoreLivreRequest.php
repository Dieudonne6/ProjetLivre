<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLivreRequest extends FormRequest
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
            'nomL' => 'required|string|max:255',
            'categorieL' => 'required|string|max:255',
            'description' => 'required|string|max:300',
            'path' => 'required|file|mimes:pdf|max:10000',
            'statutL' => 'required|integer',
            'date' => 'required|date',
            'prixL' => 'required|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'nomL.required' => 'Le nom du livre est obligatoire',
            'categorieL.required' => 'La catégorie est obligatoire',
            'description.required' => 'La description est obligatoire',
            'path.required' => 'Le fichier PDF est obligatoire',
            'path.mimes' => 'Le fichier doit être un PDF',
            'prixL.numeric' => 'Le prix doit être un nombre',
        ];
    }
}
