<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livre extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomL',
        'categorieL',
        'description',
        'path',
        'date',
        'prixL',
        'id_vendeur'
    ];

        // Définir la relation avec le modèle Categorie
        public function categorie()
        {
            return $this->belongsTo(Categorie::class, 'categorieL');
        }

        public function vendeur()
        {
            return $this->belongsTo(User::class, 'id_vendeur');
        }
}
