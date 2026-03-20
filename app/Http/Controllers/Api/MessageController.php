<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
            // LISTE DES MESSAGES
/**
 * @OA\Get(
 *      path="/api/listemessage",
 *      tags={"message"},
 *      summary="Liste des messages",
 *      @OA\Response(
 *          response=200,
 *          description="Liste des messages retournée avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="listemessages", type="array", @OA\Items(type="object")),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      )
 * )
 */

    public function listemessage() {
        $listemessages = Message::get();

        return response()->json ([
            // 'status' => 200,
            'listemessages' => $listemessages,
            'msg' => "liste des messages retournée avec succès"
        ], 200);

        
    }

}
