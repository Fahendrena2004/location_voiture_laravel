<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'location_id',
        'date_paiement',
        'montant',
        'mode_paiement',
        'numero_mobile',
        'numero_bordereau',
        'nom_banque',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
