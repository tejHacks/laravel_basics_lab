@inject('course', 'App\Lessons\LessonRepository')
<x-layout>
    <section class="hero">
        <div>
            <p class="eyebrow">Free interactive course · Laravel {{ Str::before(app()->version(), '.') }}</p>
            <h1>{{ $course->title() }}</h1>
            <p class="lead">{{ $course->tagline() }}</p>
            <div class="hero-actions">
                @if ($next)
                    <a class="btn btn-primary" href="{{ route('lessons.show', $next['slug']) }}">
                        {{ $done === 0 ? 'Start learning' : 'Continue' }}: {{ $next['title'] }} →
                    </a>
                @else
                    <span class="badge badge-success">🎉 Course complete</span>
                @endif
            </div>
        </div>
        <div class="progress-card">
            <div class="ring" style="--p: {{ $total ? round($done / $total * 100) : 0 }}">
                <span>{{ $total ? round($done / $total * 100) : 0 }}%</span>
            </div>
            <p><strong>{{ $done }}</strong> of {{ $total }} lessons done</p>
            @if ($done > 0)
                <form method="POST" action="{{ route('progress.reset') }}" onsubmit="return confirm('Reset your progress?')">
                    @csrf
                    <button class="link-btn">Reset progress</button>
                </form>
            @endif
        </div>
    </section>

    <div class="modules">
        @foreach ($modules as $i => $module)
            @php($moduleDone = collect($module['lessons'])->whereIn('slug', $completed)->count())
            <section class="module" style="--module: {{ $module['color'] }}">
                <header class="module-head">
                    <span class="module-num">{{ $i + 1 }}</span>
                    <div class="module-title">
                        <h2>{{ $module['title'] }}</h2>
                        <p>{{ $module['description'] }}</p>
                    </div>
                    <span class="badge badge-{{ $module['level'] }}">{{ ucfirst($module['level']) }}</span>
                </header>
                <div class="bar"><span style="width: {{ $moduleDone / max(count($module['lessons']), 1) * 100 }}%"></span></div>
                <ol class="lesson-list">
                    @foreach ($module['lessons'] as $lesson)
                        <li>
                            <a href="{{ route('lessons.show', $lesson['slug']) }}">
                                <span @class(['check', 'checked' => in_array($lesson['slug'], $completed)])>✓</span>
                                <span class="lesson-text">
                                    <strong>{{ $lesson['title'] }}</strong>
                                    <small>{{ $lesson['summary'] }}</small>
                                </span>
                                <span class="meta">{{ $lesson['minutes'] }} min</span>
                            </a>
                        </li>
                    @endforeach
                </ol>
            </section>
        @endforeach
    </div>
</x-layout>
