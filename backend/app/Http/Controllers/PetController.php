<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function index()
    {
        return response()->json(Pet::where('status', 'available')->get());
    }

    public function show($id)
    {
        return response()->json(Pet::find($id));
    }

    public function shelterPets()
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'shelter') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pets = Pet::where('shelter_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pets);
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user || $user->role !== 'shelter') {
                return response()->json(['message' => 'Unauthorized. Only shelters can add pets.'], 403);
            }

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'breed' => 'required|string|max:255',
                'age' => 'required|string|max:50',
                'gender' => 'required|string|max:50',
                'location' => 'required|string|max:255',
                'distance' => 'required|string|max:50',
                'image' => 'nullable|string|max:1024',
                'status' => 'nullable|string|in:available,pending,adopted'
            ]);

            $data['shelter_id'] = $user->id;
            $data['status'] = $data['status'] ?? 'available';

            $pet = Pet::create($data);

            return response()->json($pet, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create pet',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'breed' => 'sometimes|required|string|max:100',
                'age' => 'sometimes|required|string',
                'gender' => 'sometimes|required|string|max:10',
                'location' => 'sometimes|required|string|max:255',
                'distance' => 'sometimes|required|string|max:50',
                'image' => 'sometimes|nullable|string',
                'status' => 'sometimes|required|in:available,pending,adopted',
            ]);

            $pet = Pet::findOrFail($id);
            
            if ($pet->shelter_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $pet->update($validated);

            return response()->json($pet);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update pet',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $pet = Pet::findOrFail($id);
            
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }
            
            // Check if the authenticated user owns this pet
            if ($pet->shelter_id != auth()->id()) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'pet_shelter_id' => $pet->shelter_id,
                    'auth_id' => auth()->id()
                ], 403);
            }

            $pet->delete();

            return response()->json(['message' => 'Pet deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete pet',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
