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
 * Get authenticated user information
 * @OA\Get(
 *     path="/api/user",
 *     tags={"User"},
 *     summary="Get authenticated user data",
 *     description="Retrieve information about the authenticated user. If the user is a seller, the API returns their books. If the user is a buyer, it returns all available books.",
 *
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="User data retrieved successfully",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 description="Authenticated user information"
 *             ),
 *
 *             @OA\Property(
 *                 property="userlivre",
 *                 type="array",
 *                 @OA\Items(type="object"),
 *                 description="Books belonging to the seller"
 *             ),
 *
 *             @OA\Property(
 *                 property="allbooks",
 *                 type="array",
 *                 @OA\Items(type="object"),
 *                 description="All books available for buyers"
 *             ),
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="User books retrieved successfully"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="Unauthorized"
 *             )
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
                'msg' => 'User books retrieved successfully',
                // 'status' => 200
            ], 200);

        }

        $alllivre = Livre::all();
        // dd($userlivre);

        return response() -> json([
            'user' => $usercon,
            'allbooks' => $alllivre,
            'msg' => 'User books retrieved successfully',
            // 'status' => 200
        ], 200);

    }


        // RECHARGER SON SOLDE
/**
 * Recharge user balance
 * @OA\Put(
 *     path="/api/rechargesolde",
 *     tags={"User"},
 *     summary="Recharge user account balance",
 *     description="Add funds to the authenticated user's balance.",
 *
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"solde"},
 *
 *                 @OA\Property(
 *                     property="solde",
 *                     type="number",
 *                     example=100,
 *                     description="Amount to add to the user balance"
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Balance successfully recharged",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="Balance successfully recharged"
 *             ),
 *
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 description="Updated user information"
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
    public function rechargesolde(RechargeSoldeRequest $request)
    {
        $user = $request->user();

        $user->increment('solde', $request->solde);

        return response()->json([
            'msg' => 'Balance successfully recharged',
            'user' => $user
        ], 200);
    }
}
