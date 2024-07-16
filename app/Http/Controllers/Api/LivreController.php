<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLivreRequest;
use App\Http\Requests\UpdateLivreRequest;
use App\Models\Livre;
use Illuminate\Http\Request;

class LivreController extends Controller
{
    public function index(Livre $livre)
    {
        $livre = Livre::all();

        return response()->json([
            'status_code' => 200,
            'status_message' => 'Les livres ont été récupéré',
            'data' => $livre
        ]);
    }

    public function ind(Request $request, $livre)
    {
        $livre = $livre->where('categorieL', $livre->id)->first();
        dd($livre);

        /*return response()->json([
            'status_code' => 200,
            'data' => $livre
        ]);*/
    }

    public function store(CreateLivreRequest $request)
    {
        $livre = new Livre();

        $livre->nomL = $request->nomL;
        $livre->categorieL = $request->categorieL;
        $livre->description = $request->description;
        $livre->path = $request->path;
        $livre->statut = $request->statut;
        $livre->date = $request->date;
        $livre->prixL = $request->prixL;

        $livre->save();

        return response()->json([
            'status_code' => 200,
            'status_message' => 'Ajout effectué',
            'data' => $livre
        ]);
    }

    public function update(UpdateLivreRequest $request, Livre $livre)
    {
        //$livre= Livre::find($id);
        $livre->nomL = $request->nomL;
        $livre->categorieL = $request->categorieL;
        $livre->description = $request->description;
        $livre->path = $request->path;
        $livre->statut = $request->statut;
        $livre->date = $request->date;
        $livre->prixL = $request->prixL;

        $livre->save();

        return response()->json([
            'status_code' => 200,
            'status_message' => 'Modification effectuée',
            'data' => $livre
        ]);
    }

    public function delete(Livre $livre)
    {
        $livre->delete();

        return response()->json([
            'status_code' => 200,
            'status_message' => 'Le livre a été supprimé'
        ]);
    }
}
