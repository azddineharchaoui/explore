<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'itineraire_id',
        'nom',
        'lieu_logement',
        'endroits_a_visiter',
        'activites',
        'plats_a_essayer'
    ];

    protected $casts = [
        'endroits_a_visiter' => 'array',
        'activites' => 'array',
        'plats_a_essayer' => 'array',
    ];

    public function itineraire(): BelongsTo
    {
        return $this->belongsTo(Itineraire::class);
    }
}