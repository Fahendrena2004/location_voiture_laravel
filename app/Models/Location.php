<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'client_id',
        'date_debut',
        'date_fin',
        'tarif_total',
        'statut',
        'date_retour',
        'penalite',
        'avec_chauffeur',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function voitures()
    {
        return $this->belongsToMany(Voiture::class, 'location_voiture')->withTimestamps();
    }

    public function chauffeurs()
    {
        return $this->belongsToMany(Chauffeur::class, 'chauffeur_location')->withTimestamps();
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
