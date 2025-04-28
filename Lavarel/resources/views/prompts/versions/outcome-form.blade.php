<form method="POST" action="{{ route('outcomes.store') }}">
    @csrf
    <input type="hidden" name="prompt_version_id" value="{{ $promptVersion->id }}">
    <input type="hidden" name="prompt_id" value="{{ $promptVersion->prompt_id }}">
    <div class="mb-2">
        <label for="outcome-content">Your Outcome</label>
        <textarea id="outcome-content" name="content" class="form-control" required rows="3"></textarea>
    </div>
    <x-primary-button type="submit">Submit Outcome</x-primary-button>
</form>