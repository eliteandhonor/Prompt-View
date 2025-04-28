@if($outcomes->isEmpty())
    <p>No outcomes have been submitted for this version yet.</p>
@else
    <ul>
        @foreach($outcomes as $outcome)
            <li>
                <div>
                    <strong>
                        @if($outcome->user)
                            {{ $outcome->user->name }}
                        @else
                            Anonymous
                        @endif
                    </strong>
                    <span class="text-muted" style="font-size: 0.9em;">
                        ({{ $outcome->created_at ?? 'unknown date' }})
                    </span>
                </div>
                <div>
                    {{ $outcome->content }}
                </div>
            </li>
        @endforeach
    </ul>
@endif