<?php

use App\Http\Controllers\Api\SupportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/api/tes', [SupportController::class, 'index']);
    Route::post('/support/submit-task', [SupportController::class, 'submitTask']);
});

// Route::get('/tes', [SupportController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::post('login');
});
