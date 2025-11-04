<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/api/tes', [SupportController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    // Data
    Route::get('/tickes', [SupportController::class, 'tickets']);
    Route::post('/support/submit-task', [SupportController::class, 'submitTask']);

    // Tickets
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/ticket-create', [TicketController::class, 'store']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::put('/tickets/{id}', [TicketController::class, 'update']);
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);
    Route::post('/ticket/{id}/start', [TicketController::class, 'startTicket']);
    Route::post('/ticket/{id}/close', [TicketController::class, 'closeTicket']);
    Route::post('/ticket/{id}/escalate', [TicketController::class, 'escalateTicket']);
    Route::post('/ticket/{id}/handle-escalation', [TicketController::class, 'handleEscalatedTicket']);
    Route::post('/ticket/{id}/close-admin', [TicketController::class, 'closeTicketByAdmin']);
});

// Route::get('/tes', [SupportController::class, 'index']);

// Route::middleware('auth')->group(function () {
//    Route::post('/login', [AuthController::class, 'login']);
// });

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
