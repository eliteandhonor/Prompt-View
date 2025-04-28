<?php

namespace App\Http\Controllers;

use App\Models\Outcome;
use Illuminate\Http\Request;

class OutcomeController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        return response()->json(Outcome::all());
    }

    // Show the form for creating a new resource.
    public function create()
    {
        // For API: Not typically used, but included for completeness
        return response()->json(['message' => 'Display outcome creation form']);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        // Add validation as needed, e.g., for prompt_version_id
        $outcome = Outcome::create($request->all());
        return response()->json($outcome, 201);
    }

    // Display the specified resource.
    public function show(Outcome $outcome)
    {
        return response()->json($outcome);
    }

    // Show the form for editing the specified resource.
    public function edit(Outcome $outcome)
    {
        // For API: Not typically used, but included for completeness
        return response()->json(['message' => 'Display outcome edit form', 'outcome' => $outcome]);
    }

    // Update the specified resource in storage.
    public function update(Request $request, Outcome $outcome)
    {
        $outcome->update($request->all());
        return response()->json($outcome);
    }

    // Remove the specified resource from storage.
    public function destroy(Outcome $outcome)
    {
        $outcome->delete();
        return response()->json(null, 204);
    }
}