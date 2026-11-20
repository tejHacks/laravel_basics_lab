<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Reply to client emails', 'Update the project roadmap', 'Pay electricity bill',
                'Prepare slides for Monday', 'Fix the login bug', 'Read the queues docs',
                'Book a dentist appointment', 'Back up my laptop', 'Plan weekend trip',
                'Write unit tests for orders', 'Clean up old branches', 'Practise Blade components',
                'Order printer ink', 'Renew domain name', 'Sketch the new homepage',
            ]),
            'notes' => fake()->optional(0.5)->randomElement([
                'Should take about 30 minutes.', 'Ask Ada for the details first.',
                'Low effort, high impact.', 'Remember to check the docs.',
            ]),
            'done' => fake()->boolean(30),
            'priority' => fake()->randomElement([1, 2, 2, 3]),
            'due_on' => fake()->optional(0.7)->dateTimeBetween('-5 days', '+14 days'),
        ];
    }

    /** Task::factory()->done() */
    public function done(): static
    {
        return $this->state(['done' => true]);
    }
}
