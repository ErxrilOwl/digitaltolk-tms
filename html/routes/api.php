<?php

use App\Http\Controllers\API\LanguageController;
use App\Http\Controllers\API\TranslationController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function() {
    Route::apiResource('languages', LanguageController::class);

    Route::get('translations/export/{locale}', [TranslationController::class, 'export']);
    Route::apiResource('translations', TranslationController::class);
});

Route::prefix('auth')->group(function() {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
});
