@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Version History for: {{ $prompt->title ?? 'Prompt' }}</h2>
    <a href="{{ route('prompts.show', $prompt->id) }}">&#8592; Back to Prompt</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Version</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($versions as $version)
            <tr>
                <td>{{ $version->id }}</td>
                <td>{{ $version->version ?? '-' }}</td>
                <td>{{ $version->created_at }}</td>
                <td>
                    <a href="{{ route('prompt-versions.show', $version->id) }}">View</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">No versions found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection