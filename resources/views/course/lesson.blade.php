<x-layout :title="$lesson->title" wide>
    <div class="lesson-layout">
        <aside class="sidebar" aria-label="Course outline">
            @foreach ($modules as $m)
                <details @if ($m['id'] === $module['id']) open @endif>
                    <summary>{{ $m['title'] }}</summary>
                    <ul>
                        @foreach ($m['lessons'] as $l)
                            <li>
                                <a href="{{ route('lessons.show', $l['slug']) }}" @class(['current' => $l['slug'] === $lesson->slug])>
                                    <span @class(['dot', 'done' => in_array($l['slug'], $completed)])></span>{{ $l['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endforeach
        </aside>

        <article class="lesson" style="--module: {{ $module['color'] }}">
            <p class="eyebrow">{{ $module['title'] }} · {{ $lesson->minutes }} min read</p>
            <h1>{{ $lesson->title }}</h1>
            <p class="lead">{{ $lesson->summary }}</p>

            <div class="prose">
                {!! $lesson->html !!}
            </div>

            @if ($lesson->quiz)
                <section class="quiz" aria-label="Quiz">
                    <h2>Check your understanding</h2>
                    @foreach ($lesson->quiz as $i => $q)
                        <div class="question" data-answer="{{ $q['answer'] }}">
                            <p class="q"><span>Q{{ $i + 1 }}</span> {{ $q['q'] }}</p>
                            <div class="options">
                                @foreach ($q['options'] as $j => $option)
                                    <button type="button" class="option" data-index="{{ $j }}">
                                        <span class="letter">{{ chr(65 + $j) }}</span> {{ $option }}
                                    </button>
                                @endforeach
                            </div>
                            <p class="why" hidden>{{ $q['why'] }}</p>
                        </div>
                    @endforeach
                </section>
            @endif

            <div class="lesson-footer">
                <form method="POST" action="{{ route('lessons.complete', $lesson->slug) }}">
                    @csrf
                    @if ($isCompleted)
                        <input type="hidden" name="undo" value="1">
                        <span class="badge badge-success">✓ Completed</span>
                        <button class="link-btn">Mark as not done</button>
                    @else
                        <button class="btn btn-primary">Mark complete{{ $next ? ' & continue →' : '' }}</button>
                    @endif
                </form>
                <nav class="pager">
                    @if ($previous)
                        <a href="{{ route('lessons.show', $previous['slug']) }}">← {{ $previous['title'] }}</a>
                    @else <span></span> @endif
                    @if ($next)
                        <a href="{{ route('lessons.show', $next['slug']) }}">{{ $next['title'] }} →</a>
                    @endif
                </nav>
            </div>
        </article>
    </div>
</x-layout>
