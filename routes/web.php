<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('auth')->group(function (){
    Route::get('register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('auth.register.form');
    Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('auth.register');
    Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('auth.login.form');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('auth.login');
    Route::get('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('auth.logout');
});