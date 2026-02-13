<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\AdminController;
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

        // Shelter applications
        Route::get('/shelter/applications', [ApplicationController::class, 'shelterApplications']);
        Route::put('/shelter/applications/{id}/status', [ApplicationController::class, 'updateStatus']);

        // Admin routes
        Route::get('/admin/stats', [AdminController::class, 'getStats']);
        Route::get('/admin/users', [AdminController::class, 'getAllUsers']);
        Route::get('/admin/recent-users', [AdminController::class, 'getRecentUsers']);
        Route::get('/admin/recent-shelters', [AdminController::class, 'getRecentShelters']);
    });
