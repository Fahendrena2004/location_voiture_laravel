<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $fillable = [
        "type",
        "nom",
        "prenom",
        "raison_sociale",
        "date_naissance",
        "telephone",
        "adresse",
        "cin",
        "nif",
        "stat",
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}