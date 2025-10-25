<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Ticket routes
    Route::apiResource('tickets', TicketController::class)->names([
        'index' => 'api.tickets.index',
        'store' => 'api.tickets.store',
        'show' => 'api.tickets.show',
        'update' => 'api.tickets.update',
        'destroy' => 'api.tickets.destroy',
    ]);

    // Comment routes
    Route::get('tickets/{ticket}/comments', [CommentController::class, 'index']);
    Route::post('tickets/{ticket}/comments', [CommentController::class, 'store']);
    Route::put('tickets/{ticket}/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('tickets/{ticket}/comments/{comment}', [CommentController::class, 'destroy']);

    // Chat routes
    Route::get('tickets/{ticket}/chat', [ChatController::class, 'index']);
    Route::post('tickets/{ticket}/chat', [ChatController::class, 'store']);
    Route::get('tickets/{ticket}/chat/unread', [ChatController::class, 'unreadCount']);
});
