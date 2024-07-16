<?php

use App\Http\Controllers\Api\LivreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//Affichage des livres
Route::get('/livre',[LivreController::class, 'index']);

//Affichage des livres par catégorie
Route::get('/livre/{categorie}',[LivreController::class, 'ind']);

//Ajout de livres
Route::post('/livre/create', [LivreController::class, 'store']);

//Editer des livres
Route::put('/livre/editer/{livre}', [LivreController::class, 'update']);

//Supprimer un livre
Route::delete('/livre/{livre}', [LivreController::class, 'delete']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
