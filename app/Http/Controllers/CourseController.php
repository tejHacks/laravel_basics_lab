<?php

namespace App\Http\Controllers;

use App\Lessons\LessonRepository;
use App\Lessons\Progress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        private LessonRepository $lessons,
        private Progress $progress,
    ) {}

    public function home(): View
    {
        $outline = $this->lessons->outline();
        $completed = $this->progress->completed();
        $next = $outline->first(fn ($l) => ! in_array($l['slug'], $completed, true));

        return view('course.home', [
            'modules' => $this->lessons->modules(),
            'completed' => $completed,
            'total' => $outline->count(),
            'done' => $outline->whereIn('slug', $completed)->count(),
            'next' => $next,
        ]);
    }

    public function show(string $slug): View
    {
        $lesson = $this->lessons->find($slug) ?? abort(404);
        [$previous, $next] = $this->lessons->neighbours($slug);

        return view('course.lesson', [
            'lesson' => $lesson,
            'module' => $this->lessons->module($lesson->moduleId),
            'modules' => $this->lessons->modules(),
            'previous' => $previous,
            'next' => $next,
            'completed' => $this->progress->completed(),
            'isCompleted' => $this->progress->isCompleted($slug),
        ]);
    }

    public function complete(Request $request, string $slug): RedirectResponse
    {
        abort_unless($this->lessons->exists($slug), 404);

        if ($request->boolean('undo')) {
            $this->progress->uncomplete($slug);

            return back()->with('status', 'Marked as not completed.');
        }

        $this->progress->complete($slug);
        [, $next] = $this->lessons->neighbours($slug);

        return $next
            ? to_route('lessons.show', $next['slug'])->with('status', 'Lesson completed. On to the next one!')
            : to_route('home')->with('status', '🎉 You finished the whole course!');
    }

    public function search(Request $request): View
    {
        $validated = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $term = $validated['q'] ?? '';

        return view('course.search', [
            'term' => $term,
            'results' => $this->lessons->search($term),
        ]);
    }

    public function reset(): RedirectResponse
    {
        $this->progress->reset();

        return to_route('home')->with('status', 'Progress reset.');
    }
}
