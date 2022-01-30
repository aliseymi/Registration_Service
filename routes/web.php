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
    Route::get('email/send-verification', [\App\Http\Controllers\Auth\VerificationController::class, 'send'])->name('auth.email.send.verification');
    Route::get('email/verify', [\App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('auth.email.verification');
    Route::get('password/forget', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForgetForm'])->name('auth.forget.password.form');
    Route::post('password/forget', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLink'])->name('auth.forget.password');
    Route::get('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('auth.reset.password.form');
    Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('auth.reset.password');
});
