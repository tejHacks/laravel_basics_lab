<?php

namespace App\Lessons;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Loads the course from resources/lessons:
 *   curriculum.json  -> modules and lesson order
 *   {slug}.md        -> front matter + Markdown body + optional ```quiz block
 *
 * Custom Markdown syntax:
 *   :::tip | :::note | :::warning | :::analogy
 *   ...markdown...
 *   :::
 */
class LessonRepository
{
    private array $curriculum;

    /** @var array<string, Lesson> */
    private array $cache = [];

    public function __construct(private string $path)
    {
        $file = $path.'/curriculum.json';

        if (! is_file($file)) {
            throw new RuntimeException("Missing course curriculum at {$file}");
        }

        $this->curriculum = json_decode(file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);
    }

    public function title(): string
    {
        return $this->curriculum['title'];
    }

    public function tagline(): string
    {
        return $this->curriculum['tagline'];
    }

    /** Modules with their lesson summaries (from curriculum.json). */
    public function modules(): Collection
    {
        return collect($this->curriculum['modules']);
    }

    /** Every lesson summary, in course order. */
    public function outline(): Collection
    {
        return $this->modules()->flatMap(
            fn (array $module) => collect($module['lessons'])->map(fn (array $l) => [...$l, 'module' => $module['id']])
        )->values();
    }

    public function exists(string $slug): bool
    {
        return $this->outline()->contains('slug', $slug);
    }

    public function find(string $slug): ?Lesson
    {
        if (isset($this->cache[$slug])) {
            return $this->cache[$slug];
        }

        $entry = $this->outline()->firstWhere('slug', $slug);
        $file = "{$this->path}/{$slug}.md";

        if (! $entry || ! is_file($file)) {
            return null;
        }

        return $this->cache[$slug] = $this->parse($slug, $entry['module'], file_get_contents($file));
    }

    /** @return array{0: ?array, 1: ?array} previous and next lesson summaries */
    public function neighbours(string $slug): array
    {
        $outline = $this->outline();
        $i = $outline->search(fn ($l) => $l['slug'] === $slug);

        return [
            $i > 0 ? $outline[$i - 1] : null,
            $i !== false && $i < $outline->count() - 1 ? $outline[$i + 1] : null,
        ];
    }

    public function module(string $id): ?array
    {
        return $this->modules()->firstWhere('id', $id);
    }

    /** Simple full-text search over titles, summaries and bodies. */
    public function search(string $term): Collection
    {
        $term = Str::lower(trim($term));

        if ($term === '') {
            return collect();
        }

        return $this->outline()->filter(function (array $l) use ($term) {
            $haystack = Str::lower($l['title'].' '.$l['summary'].' '.@file_get_contents("{$this->path}/{$l['slug']}.md"));

            return Str::contains($haystack, $term);
        })->values();
    }

    private function parse(string $slug, string $moduleId, string $raw): Lesson
    {
        preg_match('/^---\n(.*?)\n---\n/s', $raw, $m);
        $meta = collect(explode("\n", $m[1] ?? ''))
            ->mapWithKeys(function (string $line) {
                [$key, $value] = array_pad(explode(':', $line, 2), 2, '');

                return [trim($key) => trim($value)];
            });

        $body = substr($raw, strlen($m[0] ?? ''));

        // Extract the quiz block.
        $quiz = [];
        $body = preg_replace_callback('/```quiz\n(.*?)\n```/s', function ($q) use (&$quiz) {
            $quiz = json_decode($q[1], true, flags: JSON_THROW_ON_ERROR);

            return '';
        }, $body);

        // :::kind ... :::  ->  callout boxes (blank lines let Markdown render inside).
        $labels = ['tip' => 'Tip', 'note' => 'Good to know', 'warning' => 'Watch out', 'analogy' => 'Think of it like…'];
        // Note: [ \t]* (not \s*) so the blank line after ::: survives; Markdown needs it
        // to end the HTML block, otherwise a following ``` fence is swallowed.
        $body = preg_replace_callback('/^:::(tip|note|warning|analogy)[ \t]*\n(.*?)\n:::[ \t]*$/ms', fn ($c) => sprintf(
            "<div class=\"callout callout-%s\"><div class=\"callout-title\">%s</div>\n\n%s\n\n</div>\n",
            $c[1], $labels[$c[1]], $c[2],
        ), $body);

        $html = Str::markdown($body, [
            'html_input' => 'allow',          // lessons are our own trusted content
            'allow_unsafe_links' => false,
        ]);

        return new Lesson(
            slug: $slug,
            title: $meta['title'] ?? Str::headline($slug),
            summary: $meta['summary'] ?? '',
            minutes: (int) ($meta['minutes'] ?? 5),
            moduleId: $moduleId,
            html: $html,
            quiz: $quiz,
        );
    }
}
