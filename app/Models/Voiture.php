<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voiture extends Model
{
    protected $fillable = [
        'marque',
        'modele',
        'immatriculation',
        'couleur',
        'nombre_places',
        'prix_journalier',
        'statut',
        'date_prochain_entretien',
        'categorie',
    ];

    public function entretiens()
    {
        return $this->hasMany(Entretien::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'location_voiture')->withTimestamps();
    }
}
