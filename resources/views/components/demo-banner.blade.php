@props(['files' => [], 'lesson' => null])
<div class="demo-banner">
    <strong>🧪 Live demo.</strong> {{ $slot }}
    @if ($files)
        <div style="margin-top:6px">Read the code:
            @foreach ($files as $file)
                <code>{{ $file }}</code>@if (! $loop->last), @endif
            @endforeach
        </div>
    @endif
    @if ($lesson)
        <div style="margin-top:6px">Lesson: <a href="{{ route('lessons.show', $lesson) }}">{{ Str::headline($lesson) }} →</a></div>
    @endif
</div>
