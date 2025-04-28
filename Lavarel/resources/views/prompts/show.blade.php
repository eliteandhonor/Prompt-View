@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-2xl py-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100">{{ $prompt->title }}</h1>
        <x-prompt-card x-bind:title="$prompt->title" x-bind:content="$prompt->content" id="prompt-content" />
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('prompts.index') }}"
               class="text-blue-600 dark:text-blue-400 hover:underline">Back to Prompts</a>
            <a href="{{ route('prompts.edit', $prompt->id) }}"
               class="text-blue-600 dark:text-blue-400 hover:underline">Edit Prompt</a>
            <a href="{{ route('prompt-versions.index') }}?prompt_id={{ $prompt->id }}"
               class="text-blue-600 dark:text-blue-400 hover:underline">Version History</a>
        </div>
    </div>
    {{-- Script removed: now handled by <x-prompt-card> --}}
@endsection