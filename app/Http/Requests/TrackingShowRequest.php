<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackingShowRequest extends FormRequest
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
            'project_id' => 'required|exists:projects,id',
            'month'      => 'required|integer|min:1|max:12',
            'year'       => 'required|integer|min:1900|max:2099',
            'category'   => 'nullable|in:day,night'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'Veuillez sélectionner un projet.',
            'project_id.exists' => 'Le projet sélectionné n\'existe pas.',
            'month.required' => 'Le mois est obligatoire.',
            'month.min' => 'Le mois doit être entre 1 et 12.',
            'month.max' => 'Le mois doit être entre 1 et 12.',
            'year.required' => 'L\'année est obligatoire.',
            'year.min' => 'L\'année doit être supérieure à 1900.',
            'year.max' => 'L\'année doit être inférieure à 2099.',
            'category.in' => 'La catégorie doit être "day" ou "night".'
        ];
    }
}