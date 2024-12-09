<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChurchApiController;
// use App\Http\Controllers\Api\VerificationController;
// use Illuminate\Foundation\Auth\EmailVerificationRequest;


use App\Http\Controllers\Api\CeremonieApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ici, vous pouvez enregistrer les routes API pour votre application. Ces
| routes sont chargées par le RouteServiceProvider et toutes seront
| assignées au groupe de middleware "api". Créez quelque chose de génial !
|
*/

// Route pour récupérer le token CSRF
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Routes publiques
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('password/reset', [AuthController::class, 'resetPassword']);

// Routes protégées par middleware d'authentification
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);

    Route::get('user_infos', [AuthController::class, 'user_auth_infos']);

    // Route de vérification d'email (décommenter si nécessaire)
    // Route::post('email/verify', [VerificationController::class, 'verify'])->middleware('throttle:6,1');

    // Ressources des églises
    Route::apiResource('churches', ChurchApiController::class);

    // recupérer les serviteurs dont is_man false et qui sont de l'église du serviteur principal en se basant sur l'id de l'église
    Route::middleware('auth:sanctum')->get('/serviteur_sécondaire/{church}', [ChurchApiController::class, 'serviteur_sécondaire']);

    //selecte contenant la liste des eglise
    Route::middleware('auth:sanctum')->get('/choisir-eglise', [ChurchApiController::class, 'choisirEglise']);

    Route::middleware('auth:sanctum')->post('/sauvegarder-eglise', [ChurchApiController::class, 'sauvegarderEgliseSelectionnee']);

    // Déclaration de la ressource cérémonies
    Route::apiResource('ceremonies', CeremonieApiController::class);
});
