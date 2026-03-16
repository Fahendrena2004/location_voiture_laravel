<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'location_id',
        'numero_facture',
        'date_facture',
        'montant_total',
        'statut',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
