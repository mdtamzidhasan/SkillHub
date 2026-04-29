<?php

use App\Http\Controllers\GoogleController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SamlController;
Route::get('/', function () {
    return view('welcome');
});

// Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


Route::group(['prefix' => 'saml2', 'middleware' => ['web']], function () {
    Route::get('{idpName}/login', [\Aacotroneo\Saml2\Http\Controllers\Saml2Controller::class, 'login'])->name('saml2_login');
    Route::get('{idpName}/logout', [\Aacotroneo\Saml2\Http\Controllers\Saml2Controller::class, 'logout'])->name('saml2_logout');
    Route::get('{idpName}/metadata', [\Aacotroneo\Saml2\Http\Controllers\Saml2Controller::class, 'metadata'])->name('saml2_metadata');
    Route::post('{idpName}/acs', [\Aacotroneo\Saml2\Http\Controllers\Saml2Controller::class, 'acs'])->name('saml2_acs');
    Route::get('{idpName}/sls', [\Aacotroneo\Saml2\Http\Controllers\Saml2Controller::class, 'sls'])->name('saml2_sls');
});


Route::get('/test-saml', function () {
    return 'SAML route works!';
});

Route::get('/saml2/test/login', function () {
    return 'SAML test login works!';
});


Route::get('/saml2', function () {
    return 'saml2 works';
});


Route::get('/saml2/test', function () {
    return 'saml2/test works';
});

Route::get('/saml2/test/login', function () {
    return 'saml2/test/login works';
});

Route::get('/saml2/{name}/login', function ($name) {
    return 'works: ' . $name;
});