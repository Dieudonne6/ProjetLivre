<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Livre;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Requests\RechargeSoldeRequest;


class UserController extends Controller
{
        //RECUPERATION DONNEE DE L'UTILISATEUR CONNECTER
 /**
     * Get user data
     * @OA\Get(
     *      path="/api/user",
     *      tags={"User"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="user", type="object"),
     *              @OA\Property(property="userlivre", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="msg", type="string"),
     *              @OA\Property(property="status", type="integer"),
     *          )
     *      ),
     * 
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string"),
     *             @OA\Property(property="status", type="integer")
     *         )
     *     )
     * )
     */
    public function user(Request $request) {
        $usercon = $request->user();
        $idusercon = $usercon->id;
        $statutuser = $usercon->statut;

        // dd($iduser);

        if ($statutuser === 0) {

            $userlivre = Livre::where('id_vendeur','=', $idusercon)->get();
            // dd($userlivre);
    
            return response() -> json([
                'user' => $usercon,
                'userlivre' => $userlivre,
                'msg' => 'livre de l\'utilisateur recuperer avec succes',
                // 'status' => 200
            ], 200);

        }

        $alllivre = Livre::all();
        // dd($userlivre);

        return response() -> json([
            'user' => $usercon,
            'alllivre' => $alllivre,
            'msg' => 'livre de l\'utilisateur recuperer avec succes',
            // 'status' => 200
        ], 200);

    }


        // RECHARGER SON SOLDE
/**
 * @OA\Put(
 *      path="/api/rechargesolde",
 *      tags={"User"},
 *      security={{"bearerAuth":{}}},
 *      summary="Recharger le solde de l'utilisateur",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  @OA\Property(property="solde", type="number")
 *              ),
 *              example={
 *                  "solde": 100
 *              }
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Solde rechargé avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="utilisateurConnect", type="object"),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Erreur de validation",
 *          @OA\JsonContent(
 *              @OA\Property(property="errors", type="object"),
 *              @OA\Property(property="status", type="integer")
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
    public function rechargesolde(RechargeSoldeRequest $request)
    {
        $user = $request->user();

        $user->increment('solde', $request->solde);

        return response()->json([
            'msg' => 'Solde rechargé avec succès',
            'user' => $user
        ], 200);
    }
}
