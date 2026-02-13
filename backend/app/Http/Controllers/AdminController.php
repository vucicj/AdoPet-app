<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pet;
use App\Models\Application;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function getStats(): JsonResponse
    {
        $totalAccounts = User::count();
        $totalShelters = User::where('role', 'shelter')->count();
        $totalPets = Pet::count();
        $totalAdoptions = Application::where('status', 'approved')->count();

        return response()->json([
            'totalAccounts' => $totalAccounts,
            'totalShelters' => $totalShelters,
            'totalPets' => $totalPets,
            'totalAdoptions' => $totalAdoptions,
        ]);
    }

    public function getRecentUsers(): JsonResponse
    {
        $users = User::orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at->format('M d, Y'),
                ];
            });

        return response()->json($users);
    }

    public function getRecentShelters(): JsonResponse
    {
        $shelters = User::where('role', 'shelter')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($shelter) {
                $petsCount = Pet::where('shelter_id', $shelter->id)->count();
                return [
                    'id' => $shelter->id,
                    'name' => $shelter->name,
                    'email' => $shelter->email,
                    'pets_count' => $petsCount,
                    'created_at' => $shelter->created_at->format('M d, Y'),
                ];
            });

        return response()->json($shelters);
    }

    public function getAllUsers(): JsonResponse
    {
        $users = User::orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                $applicationsCount = Application::where('user_id', $user->id)->count();
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'applications_count' => $applicationsCount,
                    'created_at' => $user->created_at->format('M d, Y'),
                ];
            });

        return response()->json($users);
    }

    public function getRecentAccounts(): JsonResponse
    {
        $limit = request()->query('limit', 5);
        
        $accounts = User::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at->format('M d, Y'),
                ];
            });

        return response()->json($accounts);
    }
}

