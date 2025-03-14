<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Itineraire extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'titre',
        'categorie',
        'duree',
        'image_path',
    ];

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favorisParUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'liste_a_visiter', 'itineraire_id', 'user_id')->withTimestamps();
    }
}