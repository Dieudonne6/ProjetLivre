<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categorie;

class CategoryController extends Controller
{
    // RECUPERER LA LISTE DES CATEGORIES
/**
 * Get book categories
 * @OA\Get(
 *     path="/api/categories",
 *     tags={"Categories"},
 *     summary="Retrieve all book categories",
 *     description="Return the list of available book categories.",
 *
 *     @OA\Response(
 *         response=200,
 *         description="Categories retrieved successfully",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="categories",
 *                 type="array",
 *                 @OA\Items(type="object"),
 *                 description="List of categories"
 *             ),
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="Categories retrieved successfully"
 *             )
 *         )
 *     )
 * )
 */
    public function categorie(){
        $categories = Categorie::all();

        return response()->json([
            // 'idcategorie' => $categories->id,
            'categories' => $categories,
            // 'status' => 200,
            'msg' => "Categories retrieved successfully"
        ], 200);
    }
}
