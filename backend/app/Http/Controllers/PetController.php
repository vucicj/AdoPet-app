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
}
