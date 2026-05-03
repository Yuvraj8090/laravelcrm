<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\PluginController;
use App\Http\Controllers\Api\ThemeController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('api.token')->group(function () {
    Route::get('/contents', [ContentController::class, 'index']);
    Route::get('/contents/{content}', [ContentController::class, 'show']);
    Route::get('/themes', [ThemeController::class, 'index']);
    Route::get('/plugins', [PluginController::class, 'index']);
});
