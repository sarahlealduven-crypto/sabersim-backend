<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\EstadisticaController;
use App\Http\Controllers\Api\ExamenController;
use App\Http\Controllers\Api\MateriaController;
use App\Http\Controllers\Api\MaterialApoyoController;
use App\Http\Controllers\Api\TutorController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'store']);
    Route::post('/login', [LoginController::class, 'store']);
    Route::post('/logout', [LogoutController::class, 'destroy'])->middleware('auth:sanctum');
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [PasswordResetController::class, 'reset']);
});

Route::prefix('v1')->group(function () {
    Route::get('/materias', [MateriaController::class, 'index']);
    Route::get('/materias/{materia}', [MateriaController::class, 'show']);
    Route::get('/materiales', [MaterialApoyoController::class, 'index']);
    Route::get('/materiales/{material}', [MaterialApoyoController::class, 'show']);
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::apiResource('examenes', ExamenController::class)->only(['store', 'index', 'show'])
        ->parameters(['examenes' => 'examen']);

    Route::prefix('examenes/{examen}')->group(function () {
        Route::post('/respuesta', [ExamenController::class, 'submitRespuesta']);
        Route::post('/finalizar', [ExamenController::class, 'finalizar']);
        Route::post('/abandonar', [ExamenController::class, 'abandonar']);
    });

    Route::get('/estadisticas', [EstadisticaController::class, 'index']);
    Route::get('/estadisticas/{materia}', [EstadisticaController::class, 'show']);

    Route::middleware('no_active_exam')->prefix('tutor')->group(function () {
        Route::post('/ask', [TutorController::class, 'ask']);
        Route::post('/continue/{conversationId}', [TutorController::class, 'continue']);
        Route::get('/conversations', [TutorController::class, 'conversations']);
    });
});
