<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
        // Ajout de livre au panier
/**
     * Add book to cart
     * @OA\Post(
     *      path="/api/addcart/{id}",
     *      tags={"Panier"},
     *          security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Livre ajouté au panier avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="panier", type="object"),
     *              @OA\Property(property="status", type="integer"),
     *              @OA\Property(property="msg", type="string"),
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
    public function addart(Request $request, $id){
        $userconnect = $request->user();

        $idlivre = $id;
        $infolivre = Livre::find($idlivre);

        $cart = new Panier();
        $cart->nom = $infolivre->nomL;
        $cart->url = $infolivre->path;
        $cart->prix = $infolivre->prixL;
        $cart->idlivre = $infolivre->id;
        $cart->idvendeur = $infolivre->id_vendeur;
        $cart->idacheteur = $userconnect->id;

        $cart->save();

        return response() ->json ([
            'panier' => $cart,
            // 'status' => 200,
            'msg' => 'livre ajouter au panier avec succes'
        ], 201);
    }

    // voir les livres du panier
 /**
     * View cart
     * @OA\Get(
     *      path="/api/cart",
     *      tags={"Panier"},
     *          security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Affichage des livres ajoutés au panier",
     *          @OA\JsonContent(
     *              @OA\Property(property="panier", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="prixtotal", type="number"),
     *              @OA\Property(property="status", type="integer"),
     *              @OA\Property(property="msg", type="string"),
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
    public function viewcart(Request $request) {
        $acheteur = $request->user();
        $idacheteur = $acheteur->id;

        $livresPanier = Panier::where('idacheteur', $idacheteur)->get();
        $prixtotal = Panier::where('idacheteur', $idacheteur)->sum('prix');

        return response()->json([
            'livresPanier' => $livresPanier,
            'prixtotal' => $prixtotal,
            // 'status' => 200, 
            'msg' => 'affichage des livres ajouter au panier'
        ], 200);    
    }

    // supprimer un livre du panier
 /**
     * Remove book from cart
     * @OA\Delete(
     *      path="/api/deletelivrecart/{id}",
     *      tags={"Panier"},
     *          security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Livre retiré du panier avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="msg", type="string"),
     *              @OA\Property(property="status", type="integer"),
     *          )
     *      ),
     *      @OA\Response(
     *           response=401,
     *           description="Unauthorized",
     *           @OA\JsonContent(
     *               @OA\Property(property="msg", type="string"),
     *               @OA\Property(property="status", type="integer")
     *          )
     *      )
     * )
     */
    public function deletelivrecart ($id) {
        $livrepanier = Panier::findOrFail($id);
        $livrepanier->delete();

        return response()->json([
            // 'status' => 200,
            'msg' => 'livre supprimer du panier avec succes',
        ], 204);
    }
}
