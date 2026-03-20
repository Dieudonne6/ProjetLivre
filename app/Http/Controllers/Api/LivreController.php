<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Livre;
use App\Models\Categorie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Requests\StoreLivreRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\LivreResource;
use App\Services\LivreService;

class LivreController extends Controller
{

    private $livreService;

    public function __construct(LivreService $livreService)
    {
        $this->livreService = $livreService;
    }

    // AJOUT D'UN LIVRE
/**
 * Create a new book
 * @OA\Post(
 *      path="/api/createlivre",
 *      tags={"Livre"},
 *      security={{"bearerAuth":{}}},
 *      @OA\RequestBody(
 *           required=true,
 *           @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(property="nomL", type="string"),
 *                  @OA\Property(property="categorieL", type="string"),
 *                  @OA\Property(property="description", type="string"),
 *                  @OA\Property(property="path", type="string", format="binary"),
 *                  @OA\Property(property="statutL", type="integer"),
 *                  @OA\Property(property="date", type="string", format="date"),
 *                  @OA\Property(property="prixL", type="number")
 *              ),
 *              example={
 *                  "nomL":"Titre du Livre",
 *                  "categorieL":"Categorie",
 *                  "description":"Description du livre",
 *                  "path":"file.pdf",
 *                  "statutL":1,
 *                  "date":"2024-07-24",
 *                  "prixL":100
 *              }
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Livre ajouté avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="msg", type="string"),
 *              @OA\Property(property="status", type="integer"),
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Validation error",
 *          @OA\JsonContent(
 *              @OA\Property(property="errors", type="object"),
 *              @OA\Property(property="status", type="integer"),
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *           description="Unauthorized",
 *           @OA\JsonContent(
 *               @OA\Property(property="msg", type="string"),
 *               @OA\Property(property="status", type="integer")
 *          )
 *      )
 * )
 */

public function createlivre(StoreLivreRequest $request)
{
    $livre = $this->livreService->createLivre(
        $request,
        $request->user()
    );

    return response()->json([
        'msg' => 'Livre ajouté avec succès',
        'livre' => new LivreResource($livre)
    ],201);
}

    // public function createlivre(StoreLivreRequest $request) {
    //     $user = $request->user();
    //     $iduser = $user->id;

    //         $file = $request->file('path');
    //         $filename = time().'_'.$file->getClientOriginalName();

    //         $path = $file->storeAs('livres', $filename, 'public');
    //         // livre GRATUIT statutL == 0 et prixl == 0
    //         // livre PREMIUM statutL == 1 et prixL == $request->prixL

    //         $user = Livre::create([
    //             'nomL' => $request->nomL,
    //             'categorieL' => $request->categorieL,
    //             'description' => $request->description,
    //             'path' =>  $path,
    //             'date' => $request->date,
    //             'prixL' => $request->prixL,
    //             'statutL' => $request->statutL,
    //             'id_vendeur' => $iduser,
    //         ]);

    //         // Storage::disk('public')->put($imageNameLivre, file_get_contents($request->path));
    //         return response()-> json([
    //             'msg' => 'livre ajouter avec succes',  
    //             'status' => 200
    //         ]);
        
    //     // }

    // }


        // LISTE DE TOUS LES LIVRES
/**
 * @OA\Get(
 *      path="/api/listelivre",
 *      tags={"Livre"},
 *      summary="Obtenir la liste de tous les livres",
 *      @OA\Response(
 *          response=200,
 *          description="Liste des livres retournée avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="livres", type="array", @OA\Items(type="object")),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      )
 * )
 */
    public function listelivre() {

            $livres = Livre::with(['categorie','vendeur'])->get();

            return LivreResource::collection($livres);
        
        // dd($livres);
        
        // return response()->json([
        //     'livres' => $livres,
        //     // 'status' => 200,
        //     'msg' => "liste des livres retournee avec succes"
        // ], 200);
    }



    // LISTE DES LIVRES PAR CATEGORIE
/**
 * @OA\Get(
 *      path="/api/livrecategorie/{categorie}",
 *      tags={"Livre"},
 *      security={{"bearerAuth":{}}},
 *      summary="Obtenir la liste des livres par catégorie",
 *      @OA\Parameter(
 *          name="categorie",
 *          in="path",
 *          required=true,
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Livres triés par catégorie retournés avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="livreCat", type="array", @OA\Items(type="object")),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      ),
 *      @OA\Response(
*            response=401,
 *           description="Unauthorized",
 *           @OA\JsonContent(
 *               @OA\Property(property="msg", type="string"),
 *               @OA\Property(property="status", type="integer")
 *          )
 *      )
 * )
 */
    public function livrecategorie ($categorie) {

        // $category = $categorie;
        $livreCat = Livre::where('categorieL', '=', $categorie)->get();

        return response()->json([
            'livreCat' => $livreCat,
            // 'status' => 200,
            'msg' => "livre trié par categories avec succes"
        ], 200);
    }

}
