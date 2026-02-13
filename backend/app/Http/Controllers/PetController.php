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

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:1024',
            'status' => 'nullable|string|in:available,adopted'
        ]);

        $data['shelter_id'] = $user->id;
        $data['status'] = $data['status'] ?? 'available';

        $pet = Pet::create($data);

        return response()->json($pet, 201);
    }
}
