<?php

namespace App\Lessons;

use Illuminate\Contracts\Session\Session;

/**
 * Tracks completed lessons in the session (no account needed).
 */
class Progress
{
    private const KEY = 'progress.completed';

    public function __construct(private Session $session) {}

    /** @return array<int, string> */
    public function completed(): array
    {
        return $this->session->get(self::KEY, []);
    }

    public function isCompleted(string $slug): bool
    {
        return in_array($slug, $this->completed(), true);
    }

    public function complete(string $slug): void
    {
        $this->session->put(self::KEY, array_values(array_unique([...$this->completed(), $slug])));
    }

    public function uncomplete(string $slug): void
    {
        $this->session->put(self::KEY, array_values(array_diff($this->completed(), [$slug])));
    }

    public function reset(): void
    {
        $this->session->forget(self::KEY);
    }
}
