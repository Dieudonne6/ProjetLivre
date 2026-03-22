<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Livre;
use App\Models\Panier;

class CartController extends Controller
{
        // Ajout de livre au panier
/**
 * Add a book to the cart
 * @OA\Post(
 *      path="/api/addcart/{id}",
 *      tags={"Cart"},
 *      security={{"bearerAuth":{}}},
 *      summary="Add a book to the user's shopping cart",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Book ID",
 *          @OA\Schema(type="integer", example=12)
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="Book successfully added to cart",
 *          @OA\JsonContent(
 *              @OA\Property(property="msg", type="string", example="Book added to cart successfully"),
 *              @OA\Property(property="cart", type="object")
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Book not found"
 *      )
 * )
 */
    public function addart(Request $request, $id){
        $userconnect = $request->user();

        $idlivre = $id;
        $infolivre = Livre::find($idlivre);

        if (!$infolivre) {
            return response() ->json ([
                // 'status' => 200,
                'msg' => 'Book not found'
            ], 404);
        }

        $cart = new Panier();
        $cart->nom = $infolivre->nomL;
        $cart->url = $infolivre->path;
        $cart->prix = $infolivre->prixL;
        $cart->idlivre = $infolivre->id;
        $cart->idvendeur = $infolivre->id_vendeur;
        $cart->idacheteur = $userconnect->id;

        $cart->save();

        return response() ->json ([
            'cart' => $cart,
            // 'status' => 200,
            'msg' => 'Book added to cart successfully'
        ], 201);
    }

    // voir les livres du panier
/**
 * View user cart
 * @OA\Get(
 *      path="/api/cart",
 *      tags={"Cart"},
 *      security={{"bearerAuth":{}}},
 *      summary="Retrieve all books currently in the user cart",
 *      @OA\Response(
 *          response=200,
 *          description="Cart retrieved successfully",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="booksCart",
 *                  type="array",
 *                  @OA\Items(type="object")
 *              ),
 *              @OA\Property(
 *                  property="Amount",
 *                  type="number",
 *                  example=54.90
 *              ),
 *              @OA\Property(
 *                  property="msg",
 *                  type="string",
 *                  example="Books in cart retrieved successfully"
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized"
 *      )
 * )
 */
    public function viewcart(Request $request) {
        $acheteur = $request->user();
        $idacheteur = $acheteur->id;

        $livresPanier = Panier::where('idacheteur', $idacheteur)->get();
        $prixtotal = Panier::where('idacheteur', $idacheteur)->sum('prix');

        return response()->json([
            'booksCart' => $livresPanier,
            'Amount' => $prixtotal,
            // 'status' => 200, 
            'msg' => 'Books in cart retrieved successfully'
        ], 200);    
    }

    // supprimer un livre du panier
/**
 * Remove a book from the cart
 * @OA\Delete(
 *      path="/api/deletelivrecart/{id}",
 *      tags={"Cart"},
 *      security={{"bearerAuth":{}}},
 *      summary="Remove a specific book from the user's cart",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Cart item ID",
 *          @OA\Schema(type="integer", example=5)
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Book removed from cart successfully"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="item not found"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized"
 *      )
 * )
 */
    public function deletelivrecart ($id) {
        $livrepanier = Panier::find($id);
        if(!$livrepanier) {
            return response()->json([
                // 'status' => 200,
                'msg' => 'item not found',
            ], 404);
        }

        $livrepanier->delete();

        return response()->json([
            // 'status' => 200,
            'msg' => 'Book removed from cart successfully',
        ], 200);
    }
}
