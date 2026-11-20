<?php

namespace App\Lessons;

/**
 * One lesson, parsed from a Markdown file in resources/lessons.
 */
final readonly class Lesson
{
    /**
     * @param  array<int, array{q: string, options: array<int, string>, answer: int, why: string}>  $quiz
     */
    public function __construct(
        public string $slug,
        public string $title,
        public string $summary,
        public int $minutes,
        public string $moduleId,
        public string $html,
        public array $quiz,
    ) {}
}
