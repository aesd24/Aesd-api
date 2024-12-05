<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TemoignageController;
use App\Http\Controllers\AdministrateurController;
use App\Http\Controllers\CeremonieController;
use App\Http\Controllers\ChantreController;
use App\Http\Controllers\ChurchController;
use App\Http\Controllers\DonController;
use App\Http\Controllers\FideleController;
use App\Http\Controllers\OpportuniteJeuneController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProgrammeController;
use App\Http\Controllers\PropositionDeReponseController;
use App\Http\Controllers\QuizzController;
use App\Http\Controllers\ServiteurDeDieuController;
use App\Http\Controllers\SujetDeDiscussionController;
use App\Http\Controllers\ActualiteController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});



// Middleware pour protéger les routes
Route::middleware(['auth'])->group(function () {
    // Routes pour les témoignages
    Route::resource('temoignages', TemoignageController::class);

    // Routes pour les administrateurs
    Route::resource('administrateurs', AdministrateurController::class);

    // Routes pour les cérémonies
    Route::resource('ceremonies', CeremonieController::class);

    // Routes pour les chantres
    Route::resource('chantres', ChantreController::class);

    // Routes pour les églises
    Route::resource('churches', ChurchController::class);


    // Routes pour les actualités
    Route::resource('actualites', ActualiteController::class);


    // Route pour afficher le formulaire de sélection d'église
    Route::get('/choisir-eglise', [ChurchController::class, 'choisirEglise'])->name('eglise.choisir');

    // Route pour sauvegarder l'église sélectionnée
    Route::post('/sauvegarder-eglise', [ChurchController::class, 'sauvegarderEgliseSelectionnee'])->name('eglise.sauvegarder');



    // Routes pour les dons
    Route::resource('dons', DonController::class);

    // Routes pour les fidèles
    Route::resource('fideles', FideleController::class);

    // Routes pour les opportunités jeunes
    Route::resource('opportunites', OpportuniteJeuneController::class);

    // Routes pour les posts
    Route::resource('posts', PostController::class);

    // Routes pour les programmes
    Route::resource('programmes', ProgrammeController::class);

    // Routes pour les propositions de réponse
    Route::resource('propositions', PropositionDeReponseController::class);

    // Routes pour les quizzes
    Route::resource('quizzes', QuizzController::class);

    // Routes pour les serviteurs de Dieu
    Route::resource('serviteurs', ServiteurDeDieuController::class);

    // Routes pour les sujets de discussion
    Route::resource('sujets', SujetDeDiscussionController::class);


    Route::get('/discussion', [SujetDeDiscussionController::class, 'user_discussion'])->name('sujets.user_discussion');

    // Définir une route pour afficher un sujet spécifique
    Route::get('/discussion/{sujet}', [SujetDeDiscussionController::class, 'user_show_discussion'])->name('sujets.user_show_discussion');

    // Route pour ajouter un commentaire
    Route::post('/sujets/{sujet}/commentaire', [SujetDeDiscussionController::class, 'addComment'])->name('sujets.add_comment');
});
