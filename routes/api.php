<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AulaController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AlunoController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProfessorController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
     
Route::middleware('auth:api')->group( function () {
    Route::resource('users', UserController::class);
});

Route::middleware('auth:api')->group( function () {
    Route::resource('professors', ProfessorController::class);
});

Route::middleware('auth:api')->group( function () {
    Route::resource('alunos', AlunoController::class);
});

Route::middleware('auth:api')->group( function () {
    Route::get('aulas/{aula}/register', [AulaController::class, 'register']);
    Route::get('aulas/{aula}/cancel', [AulaController::class, 'cancel']);
    Route::get('aulas/{aula}/accept/{aluno}', [AulaController::class, 'accept']);
    Route::post('aulas/reject', [AulaController::class, 'reject']);
    Route::resource('aulas', AulaController::class);
});

