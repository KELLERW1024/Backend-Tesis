<?php

use App\Http\Controllers\Admin\PlanesController;
use App\Http\Controllers\Conversation\ConversationController;
use App\Http\Controllers\Conversation\IaController;
use App\Http\Controllers\Document\DocumentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;


// Ruta de prueba: usuario logueado
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Grupo de rutas Auth
Route::prefix('auth')->group(function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    //Route::post('/forgot-password',[AuthController::class,'forgotPassword']);
    Route::post('/forgot-password-code',[AuthController::class,'forgotPasswordCode']);
    Route::post('/reset-password',[AuthController::class,'resetPassword']);
    //Route::post('/verify-code',[AuthController::class,'verifyCode']);
    Route::post('/reset-password-code',[AuthController::class,'resetPasswordCode']);


});

// Grupo protegido con middleware auth
Route::middleware('auth:api')->group(function ($router) {

    // Users
    Route::post("users/{id}",[UsersController::class,"update"]);
    Route::get('/plans', [PlanesController::class, "index"]);
    Route::get('section/obtenercapitulosplan',[PlanesController::class,'obtenerCapitulosPlan']);
    Route::post('/conversation',[IaController::class,'conversation']);

    Route::post('/conversation/startconversation',[ConversationController::class,'startConversation']);

    Route::post('/conversation/savereply',[ConversationController::class,'conversationSaveReply']);



    Route::post('/exchange',[DocumentController::class,'generar']);

    
});