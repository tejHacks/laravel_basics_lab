@props(['title' => null, 'wide' => false])
@inject('course', 'App\Lessons\LessonRepository')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "$title · " : '' }}{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <style>:root { --accent: {{ config('course.accent') }}; }</style>
    <script>
        // Apply the saved theme before paint (no flash).
        try { const t = localStorage.getItem('theme'); if (t) document.documentElement.dataset.theme = t; } catch (e) {}
    </script>
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark">L</span>
            <span>{{ config('app.name') }}</span>
        </a>
        <nav class="topnav">
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home', 'lessons.*')])>Course</a>
            @foreach (config('course.demos') as [$label, $route, $pattern])
                @if (Route::has($route))
                    <a href="{{ route($route) }}" @class(['active' => request()->routeIs(...explode(',', $pattern))])>{{ $label }}</a>
                @endif
            @endforeach
        </nav>
        <form action="{{ route('search') }}" method="GET" class="search">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search lessons…" aria-label="Search lessons">
        </form>
        <button class="icon-btn" type="button" data-theme-toggle aria-label="Toggle dark mode">◐</button>
    </div>
</header>

<main @class(['container', 'container-wide' => $wide])>
    @session('status')
        <div class="flash">{{ $value }}</div>
    @endsession

    {{ $slot }}
</main>

<footer class="footer">
    <p>{{ $course->title() }} · Built with Laravel {{ app()->version() }} on PHP {{ PHP_VERSION }}</p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
