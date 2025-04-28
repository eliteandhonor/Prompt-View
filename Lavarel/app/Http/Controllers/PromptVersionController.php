<?php

namespace App\Http\Controllers;

use App\Models\PromptVersion;
use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptVersionController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        // If request wants JSON, keep API behavior
        if ($request->wantsJson()) {
            return response()->json(PromptVersion::all());
        }

        $promptId = $request->query('prompt_id');
        $prompt = $promptId ? Prompt::find($promptId) : null;
        $versions = $prompt
            ? PromptVersion::where('prompt_id', $prompt->id)->orderByDesc('created_at')->get()
            : collect();

        return view('prompts.versions.index', [
            'prompt' => $prompt,
            'versions' => $versions,
        ]);
    }

    // Show the form for creating a new resource.
    public function create()
    {
        // For API: Not typically used, but included for completeness
        return response()->json(['message' => 'Display prompt version creation form']);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prompt_id' => 'required|exists:prompts,id',
            // Add other PromptVersion fields validation as needed
        ]);
        $promptVersion = PromptVersion::create($request->all());
        return response()->json($promptVersion, 201);
    }

    // Display the specified resource.
    public function show(Request $request, PromptVersion $promptVersion)
    {
        if ($request->wantsJson()) {
            return response()->json($promptVersion);
        }

        // Fetch outcomes and comments for this prompt version
        $outcomes = \App\Models\Outcome::where('prompt_version_id', $promptVersion->id)
            ->orderByDesc('created_at')
            ->get();

        $comments = \App\Models\Comment::where('prompt_version_id', $promptVersion->id)
            ->orderBy('created_at')
            ->get();

        return view('prompts.versions.show', [
            'promptVersion' => $promptVersion,
            'outcomes' => $outcomes,
            'comments' => $comments,
        ]);
    }

    // Show the form for editing the specified resource.
    public function edit(PromptVersion $promptVersion)
    {
        // For API: Not typically used, but included for completeness
        return response()->json(['message' => 'Display prompt version edit form', 'promptVersion' => $promptVersion]);
    }

    // Update the specified resource in storage.
    public function update(Request $request, PromptVersion $promptVersion)
    {
        $promptVersion->update($request->all());
        return response()->json($promptVersion);
    }

    // Remove the specified resource from storage.
    public function destroy(PromptVersion $promptVersion)
    {
        $promptVersion->delete();
        return response()->json(null, 204);
    }
}