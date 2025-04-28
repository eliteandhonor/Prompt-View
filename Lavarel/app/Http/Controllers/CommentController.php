<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        return response()->json(Comment::all());
    }

    // Show the form for creating a new resource.
    public function create()
    {
        // For API: Not typically used, but included for completeness
        return response()->json(['message' => 'Display comment creation form']);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        // Add validation as needed, e.g., for prompt_id or prompt_version_id
        $comment = Comment::create($request->all());
        return response()->json($comment, 201);
    }

    // Display the specified resource.
    public function show(Comment $comment)
    {
        return response()->json($comment);
    }

    // Show the form for editing the specified resource.
    public function edit(Comment $comment)
    {
        // For API: Not typically used, but included for completeness
        return response()->json(['message' => 'Display comment edit form', 'comment' => $comment]);
    }

    // Update the specified resource in storage.
    public function update(Request $request, Comment $comment)
    {
        $comment->update($request->all());
        return response()->json($comment);
    }

    // Remove the specified resource from storage.
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json(null, 204);
    }
}