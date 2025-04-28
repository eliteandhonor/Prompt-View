@extends('layouts.app')

@section('content')
    <div class="container max-w-xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold mb-8 text-gray-100 font-sans">Edit Prompt</h1>

        @if ($errors->any())
            <div class="mb-6">
                <ul class="list-disc list-inside text-red-400 font-semibold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('prompts.update', $prompt->id) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block mb-2 text-sm font-bold text-neon-violet font-sans">Title</label>
                <input 
                    id="title" 
                    name="title" 
                    type="text" 
                    value="{{ old('title', $prompt->title) }}" 
                    required
                    placeholder="Prompt title"
                    class="w-full px-4 py-3 rounded-lg border-2 border-gray-700 bg-gray-900 text-gray-100 font-sans font-semibold focus:outline-none focus:ring-2 focus:ring-electric-blue placeholder-gray-500 transition-all duration-200"
                    aria-label="Prompt title"
                >
            </div>

            <div>
                <label for="description" class="block mb-2 text-sm font-bold text-neon-violet font-sans">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    required
                    placeholder="A short summary of the prompt's use or intent"
                    rows="3"
                    class="w-full px-4 py-3 rounded-lg border-2 border-gray-700 bg-gray-900 text-gray-100 font-sans font-semibold focus:outline-none focus:ring-2 focus:ring-electric-blue placeholder-gray-500 transition-all duration-200 resize-vertical"
                    aria-label="Prompt description"
                >{{ old('description', $prompt->description) }}</textarea>
            </div>

            <button 
                type="submit"
                class="w-full flex justify-center items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 active:bg-blue-700 transition-all duration-200 shadow-lg shadow-blue-900/20 text-white font-bold text-lg font-sans focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Save Changes
            </button>
        </form>

        <a href="{{ route('prompts.index') }}" class="block mt-8 text-blue-400 hover:text-blue-300 hover:underline font-sans font-semibold transition">Back to Prompts</a>
    </div>
@endsection