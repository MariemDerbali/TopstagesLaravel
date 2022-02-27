<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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


//routes publiques..

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//pour protéger les routes..
//if loged in:
Route::group(['middleware' => ['auth:sanctum', 'isCoordinateur']], function () {

    Route::get('/checkingAuthenticated', function () {
        return response()->json(['message' => 'You are in', 'status' => 200], 200);
    });
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

//CRUD coordinateur

//Get all users except stagiaire

Route::get('/users', [UserController::class, 'index']);

//get one specific user by id
Route::get('/users/{id}', [UserController::class, 'show']);

//Update user by id
Route::put('/users/{id}', [UserController::class, 'update']);

// Deactivate/Activate user by id
Route::get('/users/{id}', [UserController::class, 'toggleStatus']);

//search user by keyword
Route::get('/users/search/{name}', [UserController::class, 'search']);


//Create a user
Route::post('/users', [UserController::class, 'store']);
