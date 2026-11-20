<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * A complete resource controller (see the "Resource Controllers" lesson).
 *
 *   GET    /demos/tasks              index    demo.tasks.index
 *   GET    /demos/tasks/create       create   demo.tasks.create
 *   POST   /demos/tasks              store    demo.tasks.store
 *   GET    /demos/tasks/{task}       show     demo.tasks.show
 *   GET    /demos/tasks/{task}/edit  edit     demo.tasks.edit
 *   PUT    /demos/tasks/{task}       update   demo.tasks.update
 *   DELETE /demos/tasks/{task}       destroy  demo.tasks.destroy
 *   PATCH  /demos/tasks/{task}/toggle  (extra action)
 */
class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:all,pending,done'],
        ]);

        $tasks = Task::query()
            ->with('category')                                   // eager load: no N+1
            ->search($filters['q'] ?? null)                      // #[Scope] search
            ->when($filters['category'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->when(($filters['status'] ?? 'all') === 'pending', fn ($q) => $q->pending())
            ->when(($filters['status'] ?? 'all') === 'done', fn ($q) => $q->where('done', true))
            ->orderBy('done')
            ->orderBy('priority')
            ->orderByRaw('due_on is null, due_on')
            ->paginate(8)
            ->withQueryString();

        return view('demo.tasks.index', [
            'tasks' => $tasks,
            'categories' => Category::withCount('tasks')->orderBy('name')->get(),
            'filters' => $filters,
            'stats' => [
                'total' => Task::count(),
                'pending' => Task::pending()->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('demo.tasks.form', [
            'task' => new Task(['priority' => 2]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        $task = Task::create($request->validated());

        return to_route('demo.tasks.index')->with('status', "“{$task->title}” added.");
    }

    public function show(Task $task): View
    {
        return view('demo.tasks.show', ['task' => $task->load('category')]);
    }

    public function edit(Task $task): View
    {
        return view('demo.tasks.form', [
            'task' => $task,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return to_route('demo.tasks.show', $task)->with('status', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return to_route('demo.tasks.index')->with('status', "“{$task->title}” deleted.");
    }

    public function toggle(Task $task): RedirectResponse
    {
        $task->update(['done' => ! $task->done]);

        return back()->with('status', $task->done ? 'Nice! Task completed.' : 'Task reopened.');
    }
}
