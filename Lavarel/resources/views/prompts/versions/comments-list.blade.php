@if($comments->isEmpty())
    <p>No comments yet.</p>
@else
    <ul>
        @foreach($comments as $comment)
            <li>
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