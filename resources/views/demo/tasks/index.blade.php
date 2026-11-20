<x-layout title="Tasks demo">
    <x-demo-banner lesson="resource-controllers" :files="[
        'routes/web.php',
        'app/Http/Controllers/Demo/TaskController.php',
        'app/Http/Requests/TaskRequest.php',
        'app/Models/Task.php',
        'resources/views/demo/tasks/*.blade.php',
    ]">
        A full CRUD feature: resource controller, form request validation, Eloquent relationships, scopes, eager loading, flash messages and pagination.
    </x-demo-banner>

    <div class="row" style="justify-content: space-between; margin-bottom: 12px">
        <div>
            <h1 style="margin:0">Tasks</h1>
            <p class="muted" style="margin:0">{{ $stats['pending'] }} pending of {{ $stats['total'] }}</p>
        </div>
        <a class="btn btn-primary" href="{{ route('demo.tasks.create') }}">+ New task</a>
    </div>

    <form method="GET" action="{{ route('demo.tasks.index') }}" class="panel">
        <div class="row">
            <input class="grow" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search tasks…" aria-label="Search tasks">
            <select name="status" style="width:auto" aria-label="Status">
                @foreach (['all' => 'All', 'pending' => 'Pending', 'done' => 'Done'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? 'all') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @isset($filters['category']) <input type="hidden" name="category" value="{{ $filters['category'] }}"> @endisset
            <button class="btn">Filter</button>
        </div>
        <div class="chips" style="margin-top:12px">
            <a href="{{ route('demo.tasks.index', request()->except('category', 'page')) }}" @class(['chip', 'active' => empty($filters['category'])])>All</a>
            @foreach ($categories as $category)
                <a href="{{ route('demo.tasks.index', [...request()->except('page'), 'category' => $category->id]) }}"
                   @class(['chip', 'active' => ($filters['category'] ?? null) == $category->id])>
                    {{ $category->name }} ({{ $category->tasks_count }})
                </a>
            @endforeach
        </div>
    </form>

    <div class="panel">
        @forelse ($tasks as $task)
            <div @class(['task', 'done' => $task->done])>
                <form method="POST" action="{{ route('demo.tasks.toggle', $task) }}">
                    @csrf
                    @method('PATCH')
                    <button class="check {{ $task->done ? 'checked' : '' }}" title="Toggle done" style="cursor:pointer;background:none">✓</button>
                </form>
                <div style="flex:1; min-width:0">
                    <a class="task-title" href="{{ route('demo.tasks.show', $task) }}" style="color:inherit">{{ $task->title }}</a>
                    <div class="muted" style="font-size:.85rem">
                        @if ($task->category)
                            <span style="color: {{ $task->category->color }}">● {{ $task->category->name }}</span> ·
                        @endif
                        {{ $task->priority_label }} priority
                        @if ($task->due_on)
                            · <span @style(['color: var(--danger); font-weight:600' => $task->is_overdue])>
                                due {{ $task->due_on->isToday() ? 'today' : $task->due_on->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>
                <a class="btn btn-sm" href="{{ route('demo.tasks.edit', $task) }}">Edit</a>
            </div>
        @empty
            <p class="muted">No tasks match. <a href="{{ route('demo.tasks.create') }}">Create one?</a></p>
        @endforelse
    </div>

    {{ $tasks->links() }}
</x-layout>
