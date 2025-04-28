{{--
    Prompt Version History View
    - Lists all versions of a prompt in tabular form
    - Navigation: back to prompt
    - All dynamic data uses Blade escaping for safety
    - Simple and maintainable (KISS)
--}}
@extends('layouts.app')

@section('content')
{{-- DEBUG: Dump of $versions for validation (remove after check) --}}
@php // dump($versions); @endphp
<div class="container mx-auto max-w-3xl py-8">
    <h2 class="text-2xl font-bold mb-4">Version History for: {{ $prompt->title ?? 'Prompt' }}</h2>
    <a href="{{ route('prompts.show', $prompt->id) }}" class="text-blue-600 hover:underline mb-4 inline-block">&#8592; Back to Prompt</a>
    <table class="min-w-full divide-y divide-gray-300 bg-white shadow rounded">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 text-left">ID</th>
                <th class="py-2 px-4 text-left">Version</th>
                <th class="py-2 px-4 text-left">Created At</th>
                <th class="py-2 px-4 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($versions as $version)
            <tr class="border-b">
                <td class="py-2 px-4">{{ $version->id }}</td>
                <td class="py-2 px-4">{{ $version->version ?? '-' }}</td>
                <td class="py-2 px-4">{{ $version->created_at }}</td>
                <td class="py-2 px-4">
                    <a href="{{ route('prompt-versions.show', $version->id) }}" class="text-blue-600 hover:underline">View</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-gray-500 py-4">No versions found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection