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
            'path' => 'required|file|mimes:pdf|max:10048',
            'date' => 'required|date',
            'prixL' => 'required|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'nomL.required' => 'The book title is required.',
            'nomL.max' => 'The title must not exceed 255 characters.',
            'categorieL.required' => 'The category is required.',
            'description.required' => 'The description is required.',
            'description.max' => 'The description must not exceed 300 characters.',
            'path.required' => 'A PDF file is required.',
            'path.file' => 'The path must be a valid file.',
            'path.mimes' => 'Only PDF files are allowed.',
            'path.max' => 'The file size must be less than 10MB.',
            'date.required' => 'The publication date is required.',
            'date.date' => 'Please provide a valid date.',
            'prixL.required' => 'The price is required.',
            'prixL.numeric' => 'The price must be a number.',
            'prixL.min' => 'The price cannot be negative.',
        ];
    }
}
