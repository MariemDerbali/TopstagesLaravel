<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReponseController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\OffreStageController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ProfileTopnetController;
use App\Http\Controllers\ChefDepartementController;
use App\Http\Controllers\ProfileStagiaireController;
use App\Http\Controllers\TestPsychotechniqueController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/





//--------------------------AUTHENTIFICATION-----------------------------------

//-------------Pour le stagiaire------------------
//Route pour s'inscrire 
Route::post('/register', [AuthController::class, 'register']);
//Route pour s'authentifier 
Route::post('/login-stagiaire', [AuthController::class, 'StagiaireLogin'])->middleware('throttle:login');
//Route pour l'envoi d'un lien de vérification par e-mail 
Route::post('/stagiaire-forgot-password', [AuthController::class, 'Stagiaireforgotpassword']);
//Route pour réinitialiser le mot de passe oublié 
Route::post('/stagiaire-reset-forgottenpassword', [AuthController::class, 'Stagiaireresetforgottenpassword']);


//-------------Pour Topnet----------------------------
//Route pour s'authentifier 
Route::post('/login-locale', [AuthController::class, 'LocalLogin'])->middleware('throttle:login');
//Route pour l'envoi d'un lien de vérification par e-mail 
Route::post('/forgot-password', [AuthController::class, 'forgotpassword']);
//Route pour réinitialiser le mot de passe oublié
Route::post('/reset-forgottenpassword', [AuthController::class, 'resetforgottenpassword']);
//Route pour changer le mot de passe lors de la première connexion
Route::post('/reset-firstloginpassword/{id}', [AuthController::class, 'resetfirstloginpassword']);


//--------------Routes pour home page----------------------------

//(interface offres de stage publiées)
//Routes pour obtenir les départements
Route::get('/homepage-departements', [PublicController::class, 'GetDepartements']);
//Route pour obtenir les offres 
Route::get('/homepage-getoffres', [PublicController::class, 'getOffres']);

//Route pour postuler l'offre demandée
Route::post('/homepage-postuler', [PublicController::class, 'postOffreDemandee']);



//--------------------------Routes privés pour le Coordinateur-----------------------------------

Route::group(['middleware' => ['auth:sanctum', 'isCoordinateur']], function () {

    //Route pour vérifier que l'utilisateur authentifié est coordinateur
    Route::get('/checkingCoordinateur', function () {
        return response()->json(['message' => 'Vous êtes coordinateur', 'status' => 200], 200);
    });

    //Route pour consulter les utilisateurs
    Route::get('/users', [UserController::class, 'index']);
    //Routes pour modifier l'utilisateur
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::get('/edit-user/{id}', [UserController::class, 'edit']);
    //Route pour activer/désactiver utilisateur
    Route::put('/desactiver-user/{id}', [UserController::class, 'desactiverUser']);
    //Route pour créer l'utilisateur
    Route::post('/users', [UserController::class, 'store']);
    //Route pour obtenir la liste des rôles
    Route::get('/roles', [UserController::class, 'GetRoles']);

    //Route pour obtenir la liste des départements
    Route::get('/departements', [UserController::class, 'GetDepartements']);
});



//--------------------------Routes privés pour le Service formation-----------------------------------
Route::group(['middleware' => ['auth:sanctum', 'isServiceFormation']], function () {

    //Route pour vérifier que l'utilisateur authentifié est service formation
    Route::get('/checkingServiceFormation', function () {
        return response()->json(['message' => 'Vous êtes service formation', 'status' => 200], 200);
    });


    //Route pour créer un département
    Route::post('/departments', [DepartmentController::class, 'store']);
    //Route pour consulter les départements
    Route::get('/departments', [DepartmentController::class, 'index']);
    //Route pour modifier un département
    Route::post('/departments/{id}', [DepartmentController::class, 'update']);
    Route::get('/edit-department/{id}', [DepartmentController::class, 'edit']);

    //Route pour créer une question
    Route::post('/questions', [QuestionController::class, 'store']);
    //Route pour consulter les questions
    Route::get('/questions', [QuestionController::class, 'index']);
    //Route pour activer/désactiver une question
    Route::put('/desactiver-question/{id}', [QuestionController::class, 'desactiverQuestion']);
    //Route pour modifier une question
    Route::post('/questions/{id}', [QuestionController::class, 'update']);
    Route::get('/edit-question/{id}', [QuestionController::class, 'edit']);

    //Route pour obtenir la liste des réponses de la question spécifié par son id
    Route::get('/reponses/{id}', [QuestionController::class, 'GetReponses']);
    //Route pour activer/désactiver une réponses
    Route::put('/desactiver-reponse/{id}', [ReponseController::class, 'desactiverReponse']);
    //Route pour créer une réponse
    Route::post('/reponses', [ReponseController::class, 'store']);
    //Routes pour modifier une réponse
    Route::post('/reponses/{id}', [ReponseController::class, 'update']);
    Route::get('/edit-reponse/{id}', [ReponseController::class, 'edit']);
});

//--------------------------Routes privés pour l'Encadrant-----------------------------------
Route::group(['middleware' => ['auth:sanctum', 'isEncadrant']], function () {

    //Route pour vérifier que l'utilisateur authentifié est encadrant
    Route::get('/checkingEncadrant', function () {
        return response()->json(['message' => 'Vous êtes encadrant', 'status' => 200], 200);
    });
});



//--------------------------Routes privés pour le Chef département-----------------------------------
Route::group(['middleware' => ['auth:sanctum', 'isChefDepartement']], function () {

    //Route pour vérifier que l'utilisateur authentifié est chef département
    Route::get('/checkingChefDepartement', function () {
        return response()->json(['message' => 'Vous êtes chef département', 'status' => 200], 200);
    });

    Route::get('/encadrants', [ChefDepartementController::class, 'getEncadrants']);
});


//--------------------------Routes privés pour le Stagiaire-----------------------------------
Route::group(['middleware' => ['auth:sanctum', 'isStagiaire']], function () {

    //Route pour vérifier que l'utilisateur authentifié est stagiaire
    Route::get('/checkingStagiaire', function () {
        return response()->json(['message' => 'Vous êtes stagiaire', 'status' => 200], 200);
    });

    //Route pour mettre à jour le profil
    Route::get('/edit-profil-stagiaire/{id}', [ProfileStagiaireController::class, 'edit']);
    Route::post('/profil-stagiaire/{id}', [ProfileStagiaireController::class, 'update']);

    //Route pour obtenir les questions facile et les réponses du test psychotechnique
    Route::get('/getquestionsreponses', [TestPsychotechniqueController::class, 'indexQuestionsReponses']);
});


//--------------------------Routes pour tous les utilisateurs authentifiés----------------------------------

Route::group(['middleware' => ['auth:sanctum']], function () {

    //Route pour obtenir l'utilisateur topnet actuellement connecté
    Route::get('/currentuser', [AuthController::class, 'getCurrentUser']);

    //Route pour obtenir le stagiaire actuellement connecté
    Route::get('/currentstagiaire', [AuthController::class, 'getCurrentStagiaire']);

    //Route pour mettre à jour le profil pour l'utilisateur topnet
    Route::get('/edit-profil/{id}', [ProfileTopnetController::class, 'editProfil']);
    Route::post('/profil/{id}', [ProfileTopnetController::class, 'updateProfil']);
    //Route pour se déconnecter pour tous les utilisateurs
    Route::post('/logout', [AuthController::class, 'logout']);

    //--------------------------Routes pour l'encadrant et chef département-----------------------------------

    //Route pour créer une offre
    Route::post('/offres', [OffreStageController::class, 'store']);
    //Route pour activer/désactiver une offre
    Route::put('/desactiver-offre/{id}', [OffreStageController::class, 'desactiverOffre']);
    //Route pour publier une offre
    Route::put('/publier-offre/{id}', [OffreStageController::class, 'publierOffre']);
    //Route pour consulter les offres de stage
    Route::get('/offres', [OffreStageController::class, 'index']);
    //Route pour modifier une offre
    Route::post('/offres/{id}', [OffreStageController::class, 'update']);
    Route::get('/edit-offre/{id}', [OffreStageController::class, 'edit']);
});
