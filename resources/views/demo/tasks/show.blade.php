<x-layout :title="$task->title">
    <p><a href="{{ route('demo.tasks.index') }}">← All tasks</a></p>

    <div class="panel">
        <div class="row" style="justify-content: space-between">
            <h1 style="margin:0">{{ $task->title }}</h1>
            <span class="badge {{ $task->done ? 'badge-success' : 'badge-muted' }}">{{ $task->done ? 'Done' : 'Pending' }}</span>
        </div>
        @if ($task->notes)
            <p>{{ $task->notes }}</p>
        @endif
        <table style="width:100%; margin-top:10px">
            <tr><td class="muted">Category</td><td>{{ $task->category?->name ?? 'None' }}</td></tr>
            <tr><td class="muted">Priority</td><td>{{ $task->priority_label }}</td></tr>
            <tr><td class="muted">Due</td><td>{{ $task->due_on?->toFormattedDateString() ?? 'No due date' }}
                @if ($task->is_overdue) <span class="badge badge-advanced">Overdue</span> @endif</td></tr>
            <tr><td class="muted">Created</td><td>{{ $task->created_at->diffForHumans() }}</td></tr>
        </table>

        <div class="row" style="margin-top:16px">
            <a class="btn" href="{{ route('demo.tasks.edit', $task) }}">Edit</a>
            <form method="POST" action="{{ route('demo.tasks.toggle', $task) }}" class="inline">
                @csrf @method('PATCH')
                <button class="btn">{{ $task->done ? 'Reopen' : 'Mark done' }}</button>
            </form>
            <form method="POST" action="{{ route('demo.tasks.destroy', $task) }}" class="inline"
                  onsubmit="return confirm('Delete this task?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>

    <details class="panel">
        <summary><strong>What happened behind the scenes?</strong></summary>
        <ol>
            <li>The URL <code>/demos/tasks/{{ $task->id }}</code> matched <code>Route::resource('tasks', …)</code> → <code>show</code>.</li>
            <li><strong>Route model binding</strong> loaded <code>Task #{{ $task->id }}</code> (a missing id gives a 404).</li>
            <li>The controller eager-loaded the category with <code>$task->load('category')</code>.</li>
            <li>The <code>is_overdue</code> and <code>priority_label</code> values come from <strong>accessors</strong> on the model.</li>
        </ol>
    </details>
</x-layout>
