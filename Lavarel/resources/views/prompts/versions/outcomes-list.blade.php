{{--
    Outcomes List Partial
    - Displays a list of outcomes for a prompt version
    - All dynamic content is escaped for security
    - KISS: minimal, clear markup and logic
--}}
{{-- DEBUG: Dump $outcomes for validation (remove after check) --}}
@php // dump($outcomes); @endphp
@if($outcomes->isEmpty())
    <p>No outcomes have been submitted for this version yet.</p>
@else
    <ul>
        @foreach($outcomes as $outcome)
            <li class="mb-4">
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