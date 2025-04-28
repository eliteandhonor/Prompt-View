{{--
    Comments List Partial
    - Displays a list of comments for a prompt version
    - All dynamic content is escaped for security
    - KISS: minimal, clear markup and logic
--}}
{{-- DEBUG: Dump $comments for validation (remove after check) --}}
@php // dump($comments); @endphp
@if($comments->isEmpty())
    <p>No comments yet.</p>
@else
    <ul>
        @foreach($comments as $comment)
            <li class="mb-4">
                <div>
                    <strong>
                        @if($comment->user)
                            {{ $comment->user->name }}
                        @else
                            Guest
                        @endif
                    </strong>
                    <span class="text-muted" style="font-size: 0.9em;">
                        ({{ $comment->created_at ?? 'unknown date' }})
                    </span>
                </div>
                <div>
                    {{ $comment->content }}
                </div>
            </li>
        @endforeach
    </ul>
@endif