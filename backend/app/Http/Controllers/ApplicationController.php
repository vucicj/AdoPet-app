<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function userApplications(): JsonResponse
    {
        $user = Auth::user();
        
        $applications = Application::where('user_id', $user->id)
            ->with(['pet'])
            ->orderBy('applied_at', 'desc')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'petName' => $app->pet->name,
                    'petType' => $app->pet->breed,
                    'age' => $app->pet->age,
                    'appliedOn' => $app->applied_at->format('F d, Y'),
                    'status' => $app->status,
                    'image' => $app->pet->image,
                ];
            });

        return response()->json($applications);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
        ]);

        $user = Auth::user();

        $existingApplication = Application::where('user_id', $user->id)
            ->where('pet_id', $request->pet_id)
            ->first();

        if ($existingApplication) {
            return response()->json(['message' => 'You have already applied for this pet'], 422);
        }

        $application = Application::create([
            'user_id' => $user->id,
            'pet_id' => $request->pet_id,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Application submitted successfully', 'data' => $application], 201);
    }

    public function withdraw($id): JsonResponse
    {
        $user = Auth::user();
        $application = Application::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $application->delete();

        return response()->json(['message' => 'Application withdrawn successfully']);
    }
}
