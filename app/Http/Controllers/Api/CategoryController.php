<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categorie;

class CategoryController extends Controller
{
    // RECUPERER LA LISTE DES CATEGORIES
/**
 * @OA\Get(
 *      path="/api/categories",
 *      tags={"Categories"},
 *      summary="Obtenir la liste des catégories",
 *      @OA\Response(
 *          response=200,
 *          description="Liste des catégories retournée avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="categories", type="array", @OA\Items(type="object")),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      )
 * )
 */
    public function categorie(){
        $categories = Categorie::all();

        return response()->json([
            // 'idcategorie' => $categories->id,
            'categories' => $categories,
            // 'status' => 200,
            'msg' => "liste des categories retournee avec succes"
        ], 200);
    }
}
