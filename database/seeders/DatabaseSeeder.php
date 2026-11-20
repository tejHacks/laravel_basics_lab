<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Demo Learner',
            'email' => 'learner@example.com',
            'password' => 'password',
        ]);

        $categories = collect([
            ['name' => 'Work', 'color' => '#2563EB'],
            ['name' => 'Personal', 'color' => '#16A34A'],
            ['name' => 'Learning', 'color' => '#F53003'],
        ])->map(fn (array $c) => Category::create($c));

        $samples = [
            ['Learn routing basics', 'Learning', true, 1],
            ['Build a resource controller', 'Learning', false, 1],
            ['Write my first migration', 'Learning', false, 2],
            ['Send weekly report', 'Work', false, 1],
            ['Review pull requests', 'Work', false, 2],
            ['Buy groceries', 'Personal', false, 3],
            ['Call the bank', 'Personal', true, 2],
        ];

        foreach ($samples as $i => [$title, $category, $done, $priority]) {
            Task::create([
                'title' => $title,
                'done' => $done,
                'priority' => $priority,
                'due_on' => now()->addDays($i - 2),
                'category_id' => $categories->firstWhere('name', $category)->id,
            ]);
        }

        // Plus some random tasks spread across the categories, so pagination has work to do.
        Task::factory()->count(12)->sequence(fn ($seq) => ['category_id' => $categories[$seq->index % 3]->id])->create();
    }
}
