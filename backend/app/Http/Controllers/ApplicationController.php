<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $result = DB::transaction(function () use ($request, $user) {
            $pet = Pet::lockForUpdate()->findOrFail($request->pet_id);

            if ($pet->status === 'adopted') {
                return ['error' => 'This pet is already adopted', 'code' => 422];
            }

            if ($pet->status === 'pending') {
                return ['error' => 'This pet already has a pending request', 'code' => 422];
            }

            $pendingExists = Application::where('pet_id', $pet->id)
                ->where('status', 'pending')
                ->exists();

            if ($pendingExists) {
                return ['error' => 'This pet already has a pending request', 'code' => 422];
            }

            $existingApplication = Application::where('user_id', $user->id)
                ->where('pet_id', $pet->id)
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existingApplication) {
                return ['error' => 'You have already applied for this pet', 'code' => 422];
            }

            $application = Application::create([
                'user_id' => $user->id,
                'pet_id' => $pet->id,
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

            $pet->update(['status' => 'pending']);

            return ['application' => $application];
        });

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json([
            'message' => 'Application submitted successfully',
            'data' => $result['application']
        ], 201);
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

        if ($application->pet && $application->status === 'pending') {
            $otherPendingApps = Application::where('pet_id', $application->pet_id)
                ->where('id', '!=', $application->id)
                ->where('status', 'pending')
                ->exists();
            
            if (!$otherPendingApps) {
                $application->pet->update(['status' => 'available']);
            }
        }

        $application->delete();

        return response()->json(['message' => 'Application withdrawn successfully']);
    }

    public function shelterDashboard(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'shelter') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pets = Pet::where('shelter_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $applications = Application::with(['user', 'pet'])
            ->whereHas('pet', function ($query) use ($user) {
                $query->where('shelter_id', $user->id);
            })
            ->orderBy('applied_at', 'desc')
            ->get();

        $completedAdoptions = $applications->where('status', 'approved')->count();

        $adoptedPets = $applications
            ->where('status', 'approved')
            ->map(function ($app) {
                return [
                    'application_id' => $app->id,
                    'pet_id' => $app->pet_id,
                    'pet_name' => $app->pet->name,
                    'pet_image' => $app->pet->image,
                    'adopted_by' => $app->user->name,
                    'adopted_by_user_id' => $app->user->id,
                    'adopted_at' => $app->updated_at->format('F d, Y'),
                ];
            })
            ->values();

        $applicationPayload = $applications->map(function ($app) {
            return [
                'id' => $app->id,
                'pet_id' => $app->pet_id,
                'user_name' => $app->user->name,
                'user_id' => $app->user->id,
                'pet_name' => $app->pet->name,
                'pet_image' => $app->pet->image,
                'pet_status' => $app->pet->status,
                'status' => $app->status,
                'applied_at' => $app->applied_at->format('F d, Y'),
                'adopted_at' => $app->status === 'approved'
                    ? $app->updated_at->format('F d, Y')
                    : null,
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

        return response()->json([
            'pets' => $pets,
            'applications' => $applicationPayload,
            'completed_adoptions' => $completedAdoptions,
            'adopted_pets' => $adoptedPets,
        ]);
    }

    public function shelterApplications(): JsonResponse
    {
        $user = Auth::user();

        $applications = Application::with(['user', 'pet'])
            ->whereHas('pet', function ($query) use ($user) {
                $query->where('shelter_id', $user->id);
            })
            ->orderBy('applied_at', 'desc')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'pet_id' => $app->pet_id,
                    'user_name' => $app->user->name,
                    'user_id' => $app->user->id,
                    'pet_name' => $app->pet->name,
                    'pet_image' => $app->pet->image,
                    'pet_status' => $app->pet->status,
                    'status' => $app->status,
                    'applied_at' => $app->applied_at->format('F d, Y'),
                    'adopted_at' => $app->status === 'approved'
                        ? $app->updated_at->format('F d, Y')
                        : null,
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
            'status' => 'required|in:approved,rejected',
        ]);

        $user = Auth::user();

        $result = DB::transaction(function () use ($id, $request, $user) {
            $application = Application::lockForUpdate()->find($id);

            if (!$application) {
                return ['error' => 'Application not found', 'code' => 404];
            }

            $pet = Pet::lockForUpdate()->find($application->pet_id);

            if (!$pet) {
                return ['error' => 'Pet not found', 'code' => 404];
            }

            if ($pet->shelter_id !== $user->id) {
                return ['error' => 'Unauthorized', 'code' => 403];
            }

            if ($application->status !== 'pending') {
                return ['error' => 'Only pending requests can be updated', 'code' => 422];
            }

            if ($request->status === 'approved') {
                $application->update(['status' => 'approved']);

                $pet->update([
                    'status' => 'adopted',
                    'adopted_by_user_id' => $application->user_id,
                ]);

                Application::where('pet_id', $pet->id)
                    ->where('id', '!=', $application->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);
            } else {
                $application->update(['status' => 'rejected']);

                $otherPendingApps = Application::where('pet_id', $pet->id)
                    ->where('id', '!=', $application->id)
                    ->where('status', 'pending')
                    ->exists();

                if (!$otherPendingApps && $pet->status !== 'adopted') {
                    $pet->update([
                        'status' => 'available',
                        'adopted_by_user_id' => null,
                    ]);
                }
            }

            return ['application' => $application];
        });

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json([
            'message' => 'Application status updated successfully',
            'data' => $result['application']
        ]);
    }
}
