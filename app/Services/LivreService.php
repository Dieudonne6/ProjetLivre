<?php

namespace App\Services;

use App\Models\Livre;
use Illuminate\Support\Facades\Storage;

class LivreService
{

    public function createLivre($request, $user)
    {

        $file = $request->file('path');

        $filename = time().'_'.$file->getClientOriginalName();

        $path = $file->storeAs('livres', $filename, 'public');

        return Livre::create([
            'nomL' => $request->nomL,
            'categorieL' => $request->categorieL,
            'description' => $request->description,
            'path' => $path,
            'date' => $request->date,
            'prixL' => $request->prixL,
            'statutL' => $request->statutL,
            'id_vendeur' => $user->id
        ]);
    }

}