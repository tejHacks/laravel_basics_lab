{{-- One form for create AND edit: $task->exists tells us which. --}}
<x-layout :title="$task->exists ? 'Edit task' : 'New task'">
    <p><a href="{{ route('demo.tasks.index') }}">← All tasks</a></p>
    <h1>{{ $task->exists ? 'Edit task' : 'New task' }}</h1>

    <form method="POST"
          action="{{ $task->exists ? route('demo.tasks.update', $task) : route('demo.tasks.store') }}"
          class="panel" novalidate>
        @csrf
        @if ($task->exists) @method('PUT') @endif

        <div class="field">
            <label for="title">Title *</label>
            <input id="title" name="title" value="{{ old('title', $task->title) }}" @class(['invalid' => $errors->has('title')]) autofocus>
            @error('title') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="3" @class(['invalid' => $errors->has('notes')])>{{ old('notes', $task->notes) }}</textarea>
            @error('notes') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="row">
            <div class="field grow">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" @class(['invalid' => $errors->has('category_id')])>
                    <option value="">None</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $task->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="field grow">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    @foreach (\App\Models\Task::PRIORITIES as $value => $label)
                        <option value="{{ $value }}" @selected(old('priority', $task->priority) == $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('priority') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="field grow">
                <label for="due_on">Due date</label>
                <input id="due_on" type="date" name="due_on" value="{{ old('due_on', $task->due_on?->format('Y-m-d')) }}" @class(['invalid' => $errors->has('due_on')])>
                @error('due_on') <p class="error">{{ $message }}</p> @enderror
            </div>
        </div>

        @if ($task->exists)
            <div class="field">
                <label style="display:flex; gap:8px; align-items:center; font-weight:500">
                    <input type="checkbox" name="done" value="1" @checked(old('done', $task->done))> Completed
                </label>
            </div>
        @endif

        <div class="row">
            <button class="btn btn-primary">{{ $task->exists ? 'Save changes' : 'Add task' }}</button>
            <a href="{{ $task->exists ? route('demo.tasks.show', $task) : route('demo.tasks.index') }}" class="btn">Cancel</a>
        </div>
    </form>
    <p class="muted" style="font-size:.9rem">Tip: submit an empty title to see validation errors and <code>old()</code> input in action.</p>
</x-layout>
