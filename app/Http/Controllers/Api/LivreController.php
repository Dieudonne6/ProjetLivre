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
 *     path="/api/createlivre",
 *     tags={"Books"},
 *     summary="Create a new book",
 *     description="Allow a seller to upload and publish a new book.",
 *
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"nomL","categorieL","description","path","statutL","date","prixL"},
 *
 *                 @OA\Property(
 *                     property="nomL",
 *                     type="string",
 *                     example="The Art of Programming"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="categorieL",
 *                     type="integer",
 *                     example=2,
 *                     description="Category ID"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="description",
 *                     type="string",
 *                     example="A complete guide to modern programming concepts."
 *                 ),
 *
 *                 @OA\Property(
 *                     property="path",
 *                     type="string",
 *                     format="binary",
 *                     description="PDF file of the book"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="statutL",
 *                     type="integer",
 *                     example=1,
 *                     description="1 = published, 0 = draft"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="date",
 *                     type="string",
 *                     format="date",
 *                     example="2026-03-22"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="prixL",
 *                     type="number",
 *                     example=2500
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Book created successfully",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="Book successfully created"
 *             ),
 *
 *             @OA\Property(
 *                 property="book",
 *                 type="object"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */

public function createlivre(StoreLivreRequest $request)
{

    if ($request->user()->statut === 1) {
        return response()->json([
            'msg' => 'Unauthorized',
        ],401);
    }

    $livre = $this->livreService->createLivre(
        $request,
        $request->user()
    );

    return response()->json([
        'msg' => 'Book successfully created',
        'book' => new LivreResource($livre)
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
 * Get all books
 * @OA\Get(
 *      path="/api/listelivre",
 *      tags={"Books"},
 *      summary="Retrieve the list of all available books",
 *      @OA\Response(
 *          response=200,
 *          description="List of books retrieved successfully",
 *          @OA\JsonContent(
 *              type="array",
 *              @OA\Items(
 *                  @OA\Property(property="id", type="integer", example=1),
 *                  @OA\Property(property="nomL", type="string", example="Treasure Island"),
 *                  @OA\Property(property="description", type="string", example="A young boy finds a treasure map leading to a pirate's fortune."),
 *                  @OA\Property(property="prixL", type="number", example=15.99),
 *                  @OA\Property(property="statutL", type="integer", example=1),
 *                  @OA\Property(property="date", type="string", format="date", example="2026-03-13")
 *              )
 *          )
 *      )
 * )
 */
    public function listelivre() {

            $livres = Livre::with(['categorie','vendeur'])->get();

            return LivreResource::collection($livres);
        
    }



    // LISTE DES LIVRES PAR CATEGORIE
/**
 * Get books by category
 * @OA\Get(
 *      path="/api/livrecategorie/{categorie}",
 *      tags={"Books"},
 *      security={{"bearerAuth":{}}},
 *      summary="Retrieve books belonging to a specific category",
 *      @OA\Parameter(
 *          name="categorie",
 *          in="path",
 *          required=true,
 *          description="Category ID",
 *          @OA\Schema(type="integer", example=3)
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Books filtered by category retrieved successfully",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="books",
 *                  type="array",
 *                  @OA\Items(type="object")
 *              ),
 *              @OA\Property(
 *                  property="msg",
 *                  type="string",
 *                  example="Books filtered by category successfully retrieved"
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Category not found"
 *      )
 * )
 */
    public function livrecategorie ($categorie) {

        // $category = $categorie;
        $livreCat = Livre::where('categorieL', '=', $categorie)->get();

        if(!$livreCat) {
            return response() ->json ([
                // 'status' => 200,
                'msg' => 'Category not found'
            ], 201);
        }

        return response()->json([
            'books' => $livreCat,
            // 'status' => 200,
            'msg' => "Books filtered by category successfully retrieved"
        ], 200);
    }

}
