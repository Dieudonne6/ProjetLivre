<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Livre;
use App\Models\Panier;
use App\Models\Commande;
use App\Models\Categorie;
use App\Models\Message;

class AuthController extends Controller
{

    // INSCRIPTION



/**
     * Register user
     * @OA\Post(
     *      path="/api/register",
     *      tags={"Register"},
     *      @OA\RequestBody(
     *           required=true,
     *              @OA\MediaType(
     *                  mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      format="email"
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="string",
     *                      format="binary"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="telephone",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="statut",
     *                      type="integer"
     *                  ),
     *              ),
     *              example={
     *                  "name":"jean",
     *                  "email":"jean@gmail.com",
     *                  "image":"image-file.jpg",
     *                  "password":"password",
     *                  "telephone":"12345678",
     *                  "statut":1
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string"),
     *              @OA\Property(property="type", type="string"),
     *              @OA\Property(property="msg", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="object"),
     *          )
     *      )
     * )
     */
    public function register(RegisterRequest $request) {

            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();

            // l'utilisateur est un Vendeur statut = 0
            // l'utilisateur est un Acheteur statut = 1

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'image' => $imageName,
                'password' => Hash::make($request->password),
                'telephone' => $request->telephone,
                'statut' => $request->statut,
            ]);

            Storage::disk('public')->put($imageName, file_get_contents($request->image));
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()-> json([
                'token' => $token,
                'type' => 'Bearer',
                'msg' => 'inscription effectuer avec succes',
                // 'status' => 200
            ], 201);
           
        // }
    }


    // CONNEXION

/**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Login"},
 *     summary="Se connecter et obtenir un token d'authentification",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"email", "password"},
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     format="email",
 *                     example="user@example.com",
 *                     description="L'adresse e-mail de l'utilisateur"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string",
 *                     format="password",
 *                     example="password123",
 *                     description="Le mot de passe de l'utilisateur"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Connexion réussie et token généré",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="token",
 *                 type="string",
 *                 example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c",
 *                 description="Le token d'authentification JWT"
 *             ),
 *             @OA\Property(
 *                 property="type",
 *                 type="string",
 *                 example="Bearer",
 *                 description="Le type du token d'authentification"
 *             ),
 *             @OA\Property(
 *                 property="status",
 *                 type="integer",
 *                 example=200,
 *                 description="Code de statut HTTP"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Échec de la connexion, e-mail ou mot de passe incorrect",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="email ou mot de passe incorrect",
 *                 description="Message d'erreur"
 *             ),
 *             @OA\Property(
 *                 property="status",
 *                 type="integer",
 *                 example=401,
 *                 description="Code de statut HTTP"
 *             )
 *         )
 *     )
 * )
 */
    public function login(LoginRequest $request) {
        if(!Auth::attempt($request->only('email', 'password'))) {
            return response() -> json([
                'msg' => 'email ou mot de passe incorrect',
                // 'status' => 401
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        // generer un message de connexion pour les notifications
        $user = Message::create([
            'id_user' => $user->id,
            'message' => "Vous êtes maintenant connecté à votre compte.",
        ]);

        return response()-> json([
            'token' => $token,
            'type' => 'Bearer',
            // 'status' => 200
        ], 200)->cookie('jwt', $token);

    }






    // DECONNEXION
/**
     * Logout user
     * @OA\Post(
     *      path="/api/logout",
     *      tags={"Logout"},
     *           security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Utilisateur déconnecté",
     *          @OA\JsonContent(
     *              @OA\Property(property="msg", type="string"),
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
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'msg' => 'Utilisateur deconnecte',
            // 'status' => 200
        ], 200);
    }
    






    // public function creermessage(Request $request) {
    //     $user = $request->user();
    //     $iduser = $user->id;

    //     $user = Message::create([

    //         'message' => $request->message,
    //     ]);

    //     return response()-> json([
    //         'msg' => 'message ajouter avec succes',  
    //         'status' => 200
    //     ]);

    // }



    // MOT DE PASSE OUBLIE
/**
 * @OA\Put(
 *      path="/api/modifpassword",
 *      tags={"User"},
 *      summary="Réinitialiser le mot de passe de l'utilisateur",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  @OA\Property(property="email", type="string", format="email"),
 *                  @OA\Property(property="password", type="string")
 *              ),
 *              example={
 *                  "email": "jean@gmail.com",
 *                  "password": "newpassword"
 *              }
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Mot de passe réinitialisé avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="infoUser", type="object"),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Erreur de validation ou email non retrouvé",
 *          @OA\JsonContent(
 *              @OA\Property(property="errors", type="object"),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      )
 * )
 */
    public function modifpassword(ResetPasswordRequest $request){

        // $validation = Validator::make($request->all(), [
        //     'email' => 'required|email|max:155',
        //     'password' => 'required|min:6',
        // ]);

        // if ($validation->fails()) {
        //     $errors = $validation->errors();

        //     return response()->json([
        //         'errors' => $errors,
        //         'status' => 401
        //     ]);
        // }

        // if ($validation->passes()) {
            $emailUser = $request->email;

            $infoUser = User::where('email', $emailUser)->first();
    
            if($infoUser) {
                $infoUser->password = Hash::make($request->password);
                $infoUser->save();

                return response()->json([
                    // 'status' => 200,
                    'infoUser' => $infoUser,
                    'msg' => "mot de passe reinitialisé avec success"
                ], 200);
            }
            else {
                return response()->json([
                    // 'status' => 401,
                    'msg' => "email non retrouvé"
                ], 401);
            }
    
           
        // }
        
        
    }


    public function ajouterNotification($tacheId, $userId)
{
    // Vérifier si une notification existe déjà pour cette tâche et cet utilisateur
    $notificationExiste = Notification::where('tache_id', $tacheId)
                                       ->where('user_id', $userId)
                                       ->exists();

    if (!$notificationExiste) {
        $tache = Tache::where('id', $tacheId);
        $tachePriorite = $tache->priorite;
        $tacheDateecheance = $tache->dateEcheance;
        $Today = Carbon::today();
            // if($tacheDateecheance->greaterThan($Today)) {
                // Insérer la nouvelle notification
                Notification::create([
                    'user_id' => $userId,
                    'tache_id' => $tacheId,
                    'message' => 'Nouvelle tâche créée avec une échéance.',
                ]);
            // }

    }
}


    // 8|dodaiF8ycBNoy0yv4NlAtavn17k52Gh7UWN4wVcK1238b1ca

    // 10|sSooLCXValpIhZZfECCPbI8jJV9MWTI1Mz6Q7y7oe8a34254
}
