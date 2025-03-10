<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Itineraire extends Model
{
    use HasFactory;
    protected $table = 'itineraires';
    protected $fillable = [
        'titre', 'categorie', 'duree', 'image_path'
    ];
}
