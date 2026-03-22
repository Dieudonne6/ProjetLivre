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
 * Register a new user
 *
 * @OA\Post(
 *     path="/api/register",
 *     summary="Register a new user",
 *     description="Creates a new user account (buyer or seller)",
 *     tags={"Authentication"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name","email","password","telephone","statut","image"},
 *
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     description="Full name of the user",
 *                     example="John Doe"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     format="email",
 *                     description="User email address",
 *                     example="john.doe@gmail.com"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="User profile picture"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="password",
 *                     type="string",
 *                     format="password",
 *                     description="User account password",
 *                     example="Password123!"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="telephone",
 *                     type="string",
 *                     description="User phone number",
 *                     example="97000000"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="statut",
 *                     type="integer",
 *                     enum={0,1},
 *                     description="User type (0 = seller, 1 = buyer)",
 *                     example=1
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="User successfully registered",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="token",
 *                 type="string",
 *                 example="1|f9c2f5e4f3c9c7f9d3f9"
 *             ),
 *             @OA\Property(
 *                 property="type",
 *                 type="string",
 *                 example="Bearer"
 *             ),
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="Registration completed successfully"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "email": {"The email has already been taken."},
 *                     "password": {"The password must be at least 8 characters."}
 *                 }
 *             )
 *         )
 *     )
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
                'msg' => 'Registration completed successfully',
                // 'status' => 200
            ], 201);
           
        // }
    }


    // CONNEXION

/**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Authentication"},
 *     summary="Login user and generate authentication token",
 *     description="Authenticate a user using email and password and return a Bearer token.",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         description="User credentials",
 *         @OA\JsonContent(
 *             type="object",
 *             required={"email","password"},
 *
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email",
 *                 example="john.doe@gmail.com",
 *                 description="Registered email address of the user"
 *             ),
 *
 *             @OA\Property(
 *                 property="password",
 *                 type="string",
 *                 format="password",
 *                 example="Password123!",
 *                 description="User account password"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="token",
 *                 type="string",
 *                 example="1|Qh3kL2e9P0cTjY8pS1V",
 *                 description="Generated authentication token"
 *             ),
 *
 *             @OA\Property(
 *                 property="type",
 *                 type="string",
 *                 example="Bearer",
 *                 description="Token type used for authorization"
 *             ),
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="You are now logged in to your account.",
 *                 description="Success message"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="Invalid email or password",
 *                 description="Authentication error message"
 *             )
 *         )
 *     )
 * )
 */
    public function login(LoginRequest $request) {
        if(!Auth::attempt($request->only('email', 'password'))) {
            return response() -> json([
                'msg' => 'Invalid email or password',
                // 'status' => 401
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        // generer un message de connexion pour les notifications
        $user = Message::create([
            'id_user' => $user->id,
            'message' => "You are now logged in to your account.",
        ]);

        return response()-> json([
            'token' => $token,
            'type' => 'Bearer',
            'msg' => 'You are now logged in to your account.'
            // 'status' => 200
        ], 200)->cookie('jwt', $token);

    }






    // DECONNEXION
/**
 * Logout user
 * @OA\Post(
 *     path="/api/logout",
 *     tags={"Authentication"},
 *     summary="Logout the authenticated user",
 *     description="Invalidate the current authentication token of the logged-in user.",
 *
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="User successfully logged out",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="User successfully logged out",
 *                 description="Confirmation message"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - Token missing or invalid",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="Unauthorized",
 *                 description="Authentication error message"
 *             )
 *         )
 *     )
 * )
 */
    public function logout(Request $request)

    {
    $user = $request->user();

    // On vérifie si l'utilisateur a un token API actuel (Sanctum)
    if ($user && $user->currentAccessToken() && method_exists($user->currentAccessToken(), 'delete')) {
        $user->currentAccessToken()->delete();
    }

    return response()->json([
        'msg' => 'User successfully logged out',
    ], 200);
}


    // {
    //     $user = Auth::user();
    //     $user->currentAccessToken()->delete();

    //     return response()->json([
    //         'msg' => 'User successfully logged out',
    //         // 'status' => 200
    //     ], 200);
    // }
    






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
 * Reset user password
 * @OA\Put(
 *     path="/api/modifpassword",
 *     tags={"User"},
 *     summary="Reset user password",
 *     description="Update the password of a user using their email address.",
 *
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"email","password"},
 *
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     format="email",
 *                     example="john.doe@gmail.com",
 *                     description="User email address"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="password",
 *                     type="string",
 *                     format="password",
 *                     example="NewSecurePassword123",
 *                     description="New password"
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Password successfully reset",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="infoUser",
 *                 type="object",
 *                 description="Updated user information"
 *             ),
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="Password successfully reset"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Email not found",
 *         @OA\JsonContent(
 *
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="Email not found"
 *             )
 *         )
 *     )
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
                    'msg' => "Password successfully reset"
                ], 200);
            }
            else {
                return response()->json([
                    // 'status' => 401,
                    'msg' => "Email not found"
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
