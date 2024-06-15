<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validations = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users|max:155',
            'password' => 'required|min:6'
        ]);


        if ($validations->fails()) {
            $errors = $validations->errors();

            return response()->json([
                'errors' => $errors,
                'statut' => 401
            ]);
        }

        if ($validations->passes()) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()-> json([
                'token' => $token,
                'type' => 'Bearer'
            ]);
        }
    }
}
