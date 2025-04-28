{{--
    Prompt Version Detail View
    - Shows details for a single prompt version, including metadata and content
    - Includes outcomes and comments via partials
    - All dynamic data is escaped for safety
    - KISS: minimal, clear markup and logic
--}}
@extends('layouts.app')

@section('content')
{{-- DEBUG: Dump $promptVersion for validation (remove after check) --}}
@php // dump($promptVersion); @endphp
<div class="container mx-auto max-w-3xl py-8">
    <a href="{{ route('prompt-versions.index', ['prompt_id' => $promptVersion->prompt_id]) }}" class="text-blue-600 hover:underline mb-4 inline-block">&#8592; Version History</a>
    <h2 class="text-2xl font-bold mb-4">Prompt Version Details</h2>
    <x-prompt-card class="mb-4">
        <h4>Version: {{ $promptVersion->version ?? $promptVersion->id }}</h4>
        <p><strong>ID:</strong> {{ $promptVersion->id }}</p>
        <p><strong>Prompt ID:</strong> {{ $promptVersion->prompt_id }}</p>
        <p><strong>Created At:</strong> {{ $promptVersion->created_at }}</p>
        <p><strong>Updated At:</strong> {{ $promptVersion->updated_at }}</p>
        <p><strong>Content:</strong></p>
        <pre>{{ $promptVersion->content ?? '' }}</pre>
    </x-prompt-card>

    <div class="mb-4">
        <h5 class="font-bold">Outcomes</h5>
        @include('prompts.versions.outcomes-list', ['outcomes' => $outcomes])
        @include('prompts.versions.outcome-form', ['promptVersion' => $promptVersion])
    </div>

    <div>
        <h5 class="font-bold">Comments</h5>
        @include('prompts.versions.comments-list', ['comments' => $comments])
        @include('prompts.versions.comment-form', ['promptVersion' => $promptVersion])
    </div>
</div>
@endsection