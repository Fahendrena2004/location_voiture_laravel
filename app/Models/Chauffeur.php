<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'categorie_permis',
        'disponible',
    ];

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'chauffeur_location')->withTimestamps();
    }
}
