<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Livre;

class AuthController extends Controller
{

    // INSCRIPTION

    public function register(Request $request) {
        $validations = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users|max:155',
            // 'image' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'required|min:6',
            'telephone' => 'required|max:8',
            'statut' => 'required'
        ]);


        if ($validations->fails()) {
            $errors = $validations->errors();

            return response()->json([
                'errors' => $errors,
                'status' => 401
            ]);
        }

        if ($validations->passes()) {
            // $imageName = "kokoko";
            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
            if(($request->statut) === 1){

                // l'utilisateur est Vendeur

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'image' => $imageName,
                    'password' => Hash::make($request->password),
                    'telephone' => $request->telephone,
                    'statut' => 1,
                ]);
    
                Storage::disk('public')->put($imageName, file_get_contents($request->image));
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()-> json([
                    'token' => $token,
                    'type' => 'Bearer',
                    'msg' => 'inscription effectuer avec succes',
                    'statut' => 200
                ]);
            }

            // l'utilisateur est un Acheteur

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'image' => $imageName,
                'password' => Hash::make($request->password),
                'telephone' => $request->telephone,
                'statut' => 0,
            ]);

            Storage::disk('public')->put($imageName, file_get_contents($request->image));
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()-> json([
                'token' => $token,
                'type' => 'Bearer',
                'msg' => 'inscription effectuer avec succes',
                'status' => 200
            ]);
           
        }
    }


    // CONNEXION

    public function login(Request $request) {
        if(!Auth::attempt($request->only('email', 'password'))) {
            return response() -> json([
                'msg' => 'email ou mot de passe incorrect',
                'status' => 401
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()-> json([
            'token' => $token,
            'type' => 'Bearer',
            'status' => 200
        ])->cookie('jwt', $token);

    }



    //RECUPERATION DONNEE DE L'UTILISATEUR CONNECTER

    public function user(Request $request) {
        $user = $request->user();
        $iduser = $user->id;

        $userlivre = Livre::where('id_vendeur', $iduser);

        return response() -> json([
            'user' => $user,
            'userlivre' => $userlivre,
            'msg' => 'livre de l\'utilisateur recuperer avec succes',
            'status' => 200
        ]);

    }

    // AJOUT D'UN LIVRE

    public function createlivre(Request $request) {
        $user = $request->user();
        $iduser = $user->id;

        $validations = Validator::make($request->all(), [
            'nomL' => 'required|string',
            'categorieL' => 'required|string',
            'description' => 'required|max:300',
            'path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'statutL' => 'required',
            'date' => 'required',
            'prixL' => 'required'
            // 'id_vendeur' => 'required'
        ]);


        if ($validations->fails()) {
            $errors = $validations->errors();

            return response()->json([
                'errors' => $errors,
                'status' => 401
            ]);
        }

        if ($validations->passes()) {
            // $imageName = "kokoko";
            $imageNameLivre = Str::random(32).".".$request->path->getClientOriginalExtension();
            if(($request->statutL) === 1){

                // livre PREMIUM

                $user = Livre::create([

                    'nomL' => $request->nomL,
                    'categorieL' => $request->categorieL,
                    'description' => $request->description,
                    'path' =>  $imageNameLivre,
                    'date' => $request->date,
                    'prixL' => $request->prixL,
                    'statutL' => 1,
                    'id_vendeur' => $iduser,
                ]);
    
                Storage::disk('public')->put($imageNameLivre, file_get_contents($request->path));
                return response()-> json([
                    'msg' => 'livre ajouter avec succes',
                    'status' => 200
                ]);
            }

            // livre GRATUIT

            $user = Livre::create([

                'nomL' => $request->nomL,
                'categorieL' => $request->categorieL,
                'description' => $request->description,
                'path' =>  $imageNameLivre,
                'date' => $request->date,
                'prixL' => $request->prixL,
                'statutL' => 0,
                'id_vendeur' => $iduser,
            ]);

            Storage::disk('public')->put($imageNameLivre, file_get_contents($request->path));
            return response()-> json([
                'msg' => 'livre ajouter avec succes',  
                'status' => 200
            ]);
        
        }

    }

    // DECONNEXION

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'msg' => 'Utilisateur deconnecte',
            'status' => 200
        ]);
    }




}
