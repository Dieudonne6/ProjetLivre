<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
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
                'statut' => 401
            ]);
        }

        if ($validations->passes()) {
            // $imageName = "kokoko";
            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
            if(($request->statut) === 1){
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
                    'msg' => 'inscription effectuer avec succes'
                ]);
            }

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
                'msg' => 'inscription effectuer avec succes'
            ]);
           
        }
    }


    public function login(Request $request) {
        if(!Auth::attempt($request->only('email', 'password'))) {
            return response() -> json([
                'msg' => 'email ou mot de passe incorrect',
                'statut' => 401
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()-> json([
            'token' => $token,
            'type' => 'Bearer'
        ])->cookie('jwt', $token);

    }


}
