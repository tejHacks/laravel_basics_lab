<x-layout title="Search">
    <h1>Search</h1>
    <form action="{{ route('search') }}" method="GET" class="search-large">
        <input type="search" name="q" value="{{ $term }}" placeholder="e.g. migration, validation, middleware" autofocus>
        <button class="btn btn-primary">Search</button>
    </form>

    @if ($term !== '')
        <p class="muted">{{ $results->count() }} {{ Str::plural('result', $results->count()) }} for “{{ $term }}”</p>
        <ol class="lesson-list card">
            @forelse ($results as $lesson)
                <li>
                    <a href="{{ route('lessons.show', $lesson['slug']) }}">
                        <span class="lesson-text">
                            <strong>{{ $lesson['title'] }}</strong>
                            <small>{{ $lesson['summary'] }}</small>
                        </span>
                        <span class="meta">{{ $lesson['minutes'] }} min</span>
                    </a>
                </li>
            @empty
                <li class="empty">No lessons match. Try a different word.</li>
            @endforelse
        </ol>
    @endif
</x-layout>
