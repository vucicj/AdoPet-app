<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pet;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function deleteUser($id): JsonResponse
    {
        $admin = auth()->user();
        if ($admin->id == $id) {
            return response()->json(['message' => 'You cannot delete your own account'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function getAllPets(): JsonResponse
    {
        $pets = Pet::with('shelter:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pet) {
                return [
                    'id' => $pet->id,
                    'name' => $pet->name,
                    'species' => $pet->species,
                    'breed' => $pet->breed,
                    'age' => $pet->age,
                    'location' => $pet->location,
                    'status' => $pet->status,
                    'shelter' => $pet->shelter,
                ];
            });

        return response()->json($pets);
    }

    public function createShelter(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $shelter = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'shelter',
        ]);

        return response()->json([
            'message' => 'Shelter account created successfully',
            'user' => [
                'id'    => $shelter->id,
                'name'  => $shelter->name,
                'email' => $shelter->email,
                'role'  => $shelter->role,
            ]
        ], 201);
    }

    public function getAdoptions(): JsonResponse
    {
        $adoptions = Application::where('status', 'approved')
            ->with(['pet:id,name,breed,species', 'user:id,name,email'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'adopter_name' => $app->full_name,
                    'adopter_email' => $app->email,
                    'pet_name' => $app->pet?->name ?? 'N/A',
                    'pet_breed' => $app->pet?->breed ?? 'N/A',
                    'pet_species' => $app->pet?->species ?? 'N/A',
                    'adopted_at' => $app->updated_at->format('M d, Y'),
                ];
            });

        return response()->json($adoptions);
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

