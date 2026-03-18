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

    protected $casts = [
        'disponible' => 'boolean',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
