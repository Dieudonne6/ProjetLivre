<?php
use App\Http\Controllers\Api\AuthController;
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
Route::get('/categories', [AuthController::class, 'categorie']);
Route::get('/listelivre', [AuthController::class, 'listelivre']);
Route::get('/listemessage', [AuthController::class, 'listemessage']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/createlivre', [AuthController::class, 'createlivre']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/addcart/{id}', [AuthController::class, 'addart']);
    Route::get('/cart', [AuthController::class, 'viewcart']);
    Route::delete('/deletelivrecart/{id}', [AuthController::class, 'deletelivrecart']);
    Route::get('/paiement', [AuthController::class, 'paiement']);
    Route::post('/validatepaiement', [AuthController::class, 'validatepaiement']);
    Route::get('/livrecategorie/{categorie}', [AuthController::class, 'livrecategorie']);
    // Route::post('/creermessage', [AuthController::class, 'creermessage']);
    Route::put('/rechargesolde', [AuthController::class, 'rechargesolde']);
});
