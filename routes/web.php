<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfessorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group( function () {
    Route::get('roles/list', [RoleController::class, 'getRoles'])->name('roles.list');
    Route::resource('roles', RoleController::class)->middleware(['auth']);
});

Route::middleware('auth')->group( function () {
    Route::get('users/list', [UserController::class, 'getUsers'])->name('users.list');
    Route::resource('users', UserController::class);
});

Route::middleware('auth')->group( function () {
    Route::get('professors/list', [ProfessorController::class, 'getProfessors'])->name('professors.list');
    Route::resource('professors', ProfessorController::class)->middleware(['auth']);
});
