<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskDemoTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_and_filters_tasks(): void
    {
        $work = Category::factory()->create(['name' => 'Work']);
        Task::factory()->for($work)->create(['title' => 'Write report', 'done' => false]);
        Task::factory()->create(['title' => 'Old chore', 'done' => true]);

        $this->get(route('demo.tasks.index'))->assertOk()->assertSee('Write report')->assertSee('Old chore');

        $this->get(route('demo.tasks.index', ['status' => 'pending']))
            ->assertSee('Write report')->assertDontSee('Old chore');

        $this->get(route('demo.tasks.index', ['q' => 'chore']))
            ->assertSee('Old chore')->assertDontSee('Write report');

        $this->get(route('demo.tasks.index', ['category' => $work->id]))
            ->assertSee('Write report')->assertDontSee('Old chore');
    }

    public function test_it_creates_a_task(): void
    {
        $this->post(route('demo.tasks.store'), ['title' => '  Learn Eloquent  ', 'priority' => 1])
            ->assertRedirect(route('demo.tasks.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('tasks', ['title' => 'Learn Eloquent', 'priority' => 1, 'done' => false]);
    }

    public function test_validation_errors_are_shown(): void
    {
        $this->from(route('demo.tasks.create'))
            ->post(route('demo.tasks.store'), ['title' => '', 'priority' => 9, 'category_id' => 999])
            ->assertRedirect(route('demo.tasks.create'))
            ->assertSessionHasErrors(['title', 'priority', 'category_id']);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_it_updates_toggles_and_deletes(): void
    {
        $task = Task::factory()->create(['title' => 'Draft', 'done' => false]);

        $this->put(route('demo.tasks.update', $task), ['title' => 'Final', 'priority' => 2, 'done' => '1'])
            ->assertRedirect(route('demo.tasks.show', $task));
        $this->assertTrue($task->fresh()->done);
        $this->assertSame('Final', $task->fresh()->title);

        $this->patch(route('demo.tasks.toggle', $task))->assertRedirect();
        $this->assertFalse($task->fresh()->done);

        $this->delete(route('demo.tasks.destroy', $task))->assertRedirect(route('demo.tasks.index'));
        $this->assertModelMissing($task);
    }

    public function test_show_and_edit_pages_render(): void
    {
        $task = Task::factory()->create();

        $this->get(route('demo.tasks.show', $task))->assertOk()->assertSee($task->title);
        $this->get(route('demo.tasks.edit', $task))->assertOk()->assertSee('Save changes');
        $this->get('/demos/tasks/999999')->assertNotFound();
    }

    public function test_pending_scope_and_overdue_accessor(): void
    {
        Task::factory()->create(['done' => false, 'due_on' => now()->subDays(3)]);
        Task::factory()->done()->create();

        $this->assertSame(1, Task::pending()->count());
        $this->assertTrue(Task::pending()->first()->is_overdue);
    }
}
