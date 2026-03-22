<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
            // LISTE DES MESSAGES
/**
 * Get all messages
 * @OA\Get(
 *     path="/api/listemessage",
 *     tags={"Messages"},
 *     summary="Retrieve all messages",
 *     description="Return the list of messages stored in the system.",
 *
 *     @OA\Response(
 *         response=200,
 *         description="Messages retrieved successfully",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="messages",
 *                 type="array",
 *                 @OA\Items(type="object"),
 *                 description="List of system messages"
 *             ),
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="Messages retrieved successfully"
 *             )
 *         )
 *     )
 * )
 */

    public function listemessage() {
        $listemessages = Message::get();

        return response()->json ([
            // 'status' => 200,
            'messages' => $listemessages,
            'msg' => "Messages retrieved successfully"
        ], 200);

        
    }

}
