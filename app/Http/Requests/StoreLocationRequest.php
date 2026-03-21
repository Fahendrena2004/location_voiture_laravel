<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (auth()->user()->isClient()) {
            $client = auth()->user()->client;
            if ($client) {
                $this->merge([
                    'client_id' => $client->id,
                    'statut' => 'en attente',
                ]);
            }
        }
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
            'voitures' => 'required|array|min:1',
            'voitures.*' => 'exists:voitures,id',
            'chauffeurs' => 'nullable|array',
            'chauffeurs.*' => 'exists:chauffeurs,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'date_retour' => 'nullable|date|after_or_equal:date_debut',
            'tarif_total' => 'required|numeric|min:0',
            'statut' => 'required|in:en attente,en cours,terminée,annulée',
        ];
    }
}
