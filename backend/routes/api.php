<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')
    ->group(function (): void {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        
        Route::get('/pets', [PetController::class, 'index']);
        Route::get('/pets/{id}', [PetController::class, 'show']);
    });

Route::middleware(['api', 'auth:sanctum'])
    ->group(function (): void {
        // User profile
        Route::get('/user', [UserController::class, 'profile']);
        Route::put('/user/profile', [UserController::class, 'updateProfile']);
        Route::put('/user/password', [UserController::class, 'updatePassword']);

        // Applications
        Route::get('/applications', [ApplicationController::class, 'userApplications']);
        Route::post('/applications', [ApplicationController::class, 'store']);
        Route::delete('/applications/{id}', [ApplicationController::class, 'withdraw']);
    });
