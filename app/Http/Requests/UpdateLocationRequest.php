<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'voiture_id' => 'required|exists:voitures,id',
            'avec_chauffeur' => 'required|boolean',
            'chauffeur_id' => 'required_if:avec_chauffeur,1|nullable|exists:chauffeurs,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'date_retour' => 'nullable|date|after_or_equal:date_debut',
            'tarif_total' => 'required|numeric|min:0',
            'statut' => 'required|in:en cours,terminée,annulée',
        ];
    }
}
