<form method="POST" action="{{ route('comments.store') }}">
    @csrf
    <input type="hidden" name="prompt_version_id" value="{{ $promptVersion->id }}">
    <input type="hidden" name="prompt_id" value="{{ $promptVersion->prompt_id }}">
    <div class="mb-2">
        <label for="comment-content">Your Comment</label>
        <textarea id="comment-content" name="content" class="form-control" required rows="3"></textarea>
    </div>
    <div class="mb-2">
        <label for="author_name">Name (optional if not logged in)</label>
        <input type="text" id="author_name" name="author_name" class="form-control">
    </div>
    <x-secondary-button type="submit">Submit Comment</x-secondary-button>
</form>