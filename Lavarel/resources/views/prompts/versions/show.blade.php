@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('prompt-versions.index') }}?prompt_id={{ $promptVersion->prompt_id }}">&#8592; Version History</a>
    <h2>Prompt Version Details</h2>
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
        <h5>Outcomes</h5>
        @include('prompts.versions.outcomes-list', ['outcomes' => $outcomes])
        @include('prompts.versions.outcome-form', ['promptVersion' => $promptVersion])
    </div>

    <div>
        <h5>Comments</h5>
        @include('prompts.versions.comments-list', ['comments' => $comments])
        @include('prompts.versions.comment-form', ['promptVersion' => $promptVersion])
    </div>
</div>
@endsection