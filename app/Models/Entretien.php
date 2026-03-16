<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entretien extends Model
{
    protected $fillable = [
        'voiture_id',
        'date_entretien',
        'description',
        'cout',
    ];

    public function voiture()
    {
        return $this->belongsTo(Voiture::class);
    }
}
