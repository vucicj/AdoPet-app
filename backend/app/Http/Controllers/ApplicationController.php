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
                    'pet_id' => $app->pet_id,
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
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'residence_type' => 'required|string',
            'own_or_rent' => 'required|string',
            'has_yard' => 'required|string',
            'owned_pets_before' => 'required|string',
            'has_other_pets' => 'required|string',
            'hours_alone' => 'required|string',
            'adoption_reason' => 'required|string',
        ]);

        $user = Auth::user();

        $existingApplication = Application::where('user_id', $user->id)
            ->where('pet_id', $request->pet_id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingApplication) {
            return response()->json(['message' => 'You have already applied for this pet'], 422);
        }

        $application = Application::create([
            'user_id' => $user->id,
            'pet_id' => $request->pet_id,
            'status' => 'pending',
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'age' => $request->age,
            'residence_type' => $request->residence_type,
            'own_or_rent' => $request->own_or_rent,
            'has_yard' => $request->has_yard,
            'owned_pets_before' => $request->owned_pets_before,
            'has_other_pets' => $request->has_other_pets,
            'other_pets_details' => $request->other_pets_details,
            'hours_alone' => $request->hours_alone,
            'adoption_reason' => $request->adoption_reason,
        ]);

        if ($application->pet) {
            $application->pet->update(['status' => 'pending']);
        }

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

        if ($application->pet) {
            if ($application->status === 'pending') {
                $application->pet->update(['status' => 'available']);
            }
            // For rejected applications, pet is already available, no change needed
        }

        $application->delete();

        return response()->json(['message' => 'Application withdrawn successfully']);
    }

    public function shelterApplications(): JsonResponse
    {
        $applications = Application::with(['user', 'pet'])
            ->orderBy('applied_at', 'desc')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'pet_id' => $app->pet_id,
                    'user_name' => $app->user->name,
                    'pet_name' => $app->pet->name,
                    'pet_image' => $app->pet->image,
                    'pet_status' => $app->pet->status,
                    'status' => $app->status,
                    'applied_at' => $app->applied_at->format('F d, Y'),
                    'full_name' => $app->full_name,
                    'email' => $app->email,
                    'phone_number' => $app->phone_number,
                    'age' => $app->age,
                    'residence_type' => $app->residence_type,
                    'own_or_rent' => $app->own_or_rent,
                    'has_yard' => $app->has_yard,
                    'owned_pets_before' => $app->owned_pets_before,
                    'has_other_pets' => $app->has_other_pets,
                    'other_pets_details' => $app->other_pets_details,
                    'hours_alone' => $app->hours_alone,
                    'adoption_reason' => $app->adoption_reason,
                ];
            });

        return response()->json($applications);
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $application->update(['status' => $request->status]);

        if ($application->pet) {
            if ($request->status === 'approved') {
                $application->pet->update(['status' => 'adopted']);
            } elseif ($request->status === 'rejected') {
                $application->pet->update(['status' => 'available']);
            }
        }

        return response()->json(['message' => 'Application status updated successfully', 'data' => $application]);
    }
}
