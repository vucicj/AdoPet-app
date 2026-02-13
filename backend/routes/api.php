<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware('cors')
    ->group(function (): void {
        // Public routes - available to everyone
        Route::middleware('api')
            ->group(function (): void {
                Route::post('/register', [AuthController::class, 'register']);
                Route::post('/login', [AuthController::class, 'login']);
                
                Route::get('/pets', [PetController::class, 'index']);
                Route::get('/pets/{id}', [PetController::class, 'show']);
            });

        // Authenticated routes (all logged-in users)
        Route::middleware(['api', 'auth:sanctum'])
            ->group(function (): void {
                Route::post('/logout', [AuthController::class, 'logout']);
                
                Route::get('/user', [UserController::class, 'profile']);
                Route::put('/user/profile', [UserController::class, 'updateProfile']);
                Route::put('/user/password', [UserController::class, 'updatePassword']);
            });

        // User-specific routes
        Route::middleware(['api', 'auth:sanctum', 'role:user,admin'])
            ->group(function (): void {
                Route::get('/applications', [ApplicationController::class, 'userApplications']);
                Route::post('/applications', [ApplicationController::class, 'store']);
                Route::delete('/applications/{id}', [ApplicationController::class, 'withdraw']);
            });

        // Shelter-specific routes
        Route::middleware(['api', 'auth:sanctum', 'role:shelter,admin'])
            ->group(function (): void {
                Route::get('/shelter/applications', [ApplicationController::class, 'shelterApplications']);
                Route::put('/shelter/applications/{id}/status', [ApplicationController::class, 'updateStatus']);
                Route::get('/shelter/pets', [PetController::class, 'shelterPets']);
                Route::get('/shelter/dashboard', [ApplicationController::class, 'shelterDashboard']);
                Route::post('/pets', [PetController::class, 'store']);
                Route::put('/pets/{id}', [PetController::class, 'update']);
                Route::delete('/pets/{id}', [PetController::class, 'destroy']);
            });

        // Admin-specific routes
        Route::middleware(['api', 'auth:sanctum', 'role:admin'])
            ->group(function (): void {
                Route::get('/admin/stats', [AdminController::class, 'getStats']);
                Route::get('/admin/users', [AdminController::class, 'getAllUsers']);
                Route::get('/admin/recent-accounts', [AdminController::class, 'getRecentAccounts']);
                Route::get('/admin/recent-users', [AdminController::class, 'getRecentUsers']);
                Route::get('/admin/recent-shelters', [AdminController::class, 'getRecentShelters']);
            });
    });
