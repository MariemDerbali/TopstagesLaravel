<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReponseController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\DepartmentController;

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



// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/forgot-password', [AuthController::class, 'forgotpassword']);
Route::post('/reset-forgottenpassword', [AuthController::class, 'resetforgottenpassword']);
Route::post('/reset-firstloginpassword/{id}', [AuthController::class, 'resetfirstloginpassword']);


//Coordinateur:
Route::group(['middleware' => ['auth:sanctum', 'isCoordinateur']], function () {


    Route::get('/checkingCoordinateur', function () {
        return response()->json(['message' => 'You are coordinateur', 'status' => 200], 200);
    });

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::get('/edit-user/{id}', [UserController::class, 'edit']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/roles', [UserController::class, 'GetRoles']);
    Route::get('/departements', [UserController::class, 'GetDepartements']);
});



//Servie formation
Route::group(['middleware' => ['auth:sanctum', 'isServiceFormation']], function () {

    Route::get('/checkingServiceFormation', function () {
        return response()->json(['message' => 'You are service formation', 'status' => 200], 200);
    });



    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments/{id}', [DepartmentController::class, 'update']);
    Route::get('/edit-department/{id}', [DepartmentController::class, 'edit']);


    Route::post('/questions', [QuestionController::class, 'store']);
    Route::get('/questions', [QuestionController::class, 'index']);


    Route::post('/questions/{id}', [QuestionController::class, 'update']);
    Route::get('/edit-question/{id}', [QuestionController::class, 'edit']);
    Route::get('/reponses/{id}', [QuestionController::class, 'GetReponses']);


    Route::delete('/delete-reponse/{id}', [ReponseController::class, 'destroy']);
    Route::post('/reponses', [ReponseController::class, 'store']);
    Route::post('/reponses/{id}', [ReponseController::class, 'update']);

    Route::get('/edit-reponse/{id}', [ReponseController::class, 'edit']);
});





//Tous les utilisateurs
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/checkingAuthenticated', function () {
        return response()->json(['message' => 'You are in', 'status' => 200], 200);
    });

    Route::get('/currentuser', [AuthController::class, 'getCurrentUser']);

    Route::get('/edit-profil/{id}', [ProfileController::class, 'editProfil']);
    Route::post('/profil/{id}', [ProfileController::class, 'updateProfil']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
