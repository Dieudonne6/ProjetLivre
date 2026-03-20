<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LivreController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\PaiementController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\AuthController;


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

// Récupérer la liste des posts
Route::get('/posts', [PostController::class, 'index']);

// Ajouter un post
Route::post('posts/create', [PostController::class, 'store']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::put('/modifpassword', [AuthController::class, 'modifpassword']);
Route::get('/categories', [CategoryController::class, 'categorie']);
Route::get('/listelivre', [LivreController::class, 'listelivre']);
Route::get('/listemessage', [MessageController::class, 'listemessage']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'user']);
    Route::post('/createlivre', [LivreController::class, 'createlivre']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/addcart/{id}', [CartController::class, 'addart']);
    Route::get('/cart', [CartController::class, 'viewcart']);
    Route::delete('/deletelivrecart/{id}', [CartController::class, 'deletelivrecart']);
    Route::get('/paiement', [PaiementController::class, 'paiement']);
    Route::post('/validatepaiement', [PaiementController::class, 'validatepaiement']);
    Route::get('/livrecategorie/{categorie}', [LivreController::class, 'livrecategorie']);
    // Route::post('/creermessage', [AuthController::class, 'creermessage']);
    Route::put('/rechargesolde', [UserController::class, 'rechargesolde']);

});
