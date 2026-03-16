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
        'prix_journalier',
        'statut',
    ];

    public function entretiens()
    {
        return $this->hasMany(Entretien::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
