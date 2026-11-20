<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Demo model used by the "Tasks CRUD" demo.
 * Shows Laravel 13 attributes (#[Fillable], #[Scope]), casts, accessors and relationships.
 */
#[Fillable(['title', 'notes', 'done', 'priority', 'due_on', 'category_id'])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    public const PRIORITIES = [1 => 'High', 2 => 'Normal', 3 => 'Low'];

    protected function casts(): array
    {
        return [
            'done' => 'boolean',
            'priority' => 'integer',
            'due_on' => 'date',
        ];
    }

    /** The task belongs to a category (tasks.category_id). */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Task::pending() */
    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('done', false);
    }

    /** Task::search('report') */
    #[Scope]
    protected function search(Builder $query, ?string $term): void
    {
        $query->when($term, fn (Builder $q, string $t) => $q->where(fn (Builder $q) => $q
            ->where('title', 'like', "%{$t}%")
            ->orWhere('notes', 'like', "%{$t}%")));
    }

    /** $task->is_overdue */
    protected function isOverdue(): Attribute
    {
        return Attribute::get(fn () => ! $this->done && $this->due_on?->isPast() && ! $this->due_on->isToday());
    }

    /** $task->priority_label */
    protected function priorityLabel(): Attribute
    {
        return Attribute::get(fn () => self::PRIORITIES[$this->priority] ?? 'Normal');
    }
}
