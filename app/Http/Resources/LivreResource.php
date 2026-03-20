<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivreResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom' => $this->nomL,
            'description' => $this->description,
            'categorie' => $this->categorieL,
            'prix' => $this->prixL,
            'statut' => $this->statutL,
            'date' => $this->date,
            'vendeur' => $this->vendeur?->name,
            'pdf_url' => asset('storage/'.$this->path)
        ];
    }
}