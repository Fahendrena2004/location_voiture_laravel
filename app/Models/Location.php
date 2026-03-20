<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'client_id',
        'voiture_id',
        'avec_chauffeur',
        'chauffeur_id',
        'date_debut',
        'date_fin',
        'date_retour',
        'tarif_total',
        'penalite',
        'statut',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function voiture()
    {
        return $this->belongsTo(Voiture::class);
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function facture()
    {
        return $this->hasOne(Facture::class);
    }
}
