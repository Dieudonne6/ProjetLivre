<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;
    public $timestamps = false;

    Protected $fillable = [
        'nom'
    ];

    public function livres()
    {
        return $this->hasMany(Livre::class, 'categorieL');
    }
}
