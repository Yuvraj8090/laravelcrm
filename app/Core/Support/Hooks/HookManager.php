<?php

namespace App\Core\Support\Hooks;

class HookManager
{
    protected array $actions = [];

    protected array $filters = [];

    public function addAction(string $hook, callable $callback, int $priority = 10): void
    {
        $this->actions[$hook][$priority][] = $callback;
    }

    public function doAction(string $hook, mixed ...$args): void
    {
        foreach ($this->sortedCallbacks($this->actions[$hook] ?? []) as $callback) {
            $callback(...$args);
        }
    }

    public function addFilter(string $hook, callable $callback, int $priority = 10): void
    {
        $this->filters[$hook][$priority][] = $callback;
    }

    public function applyFilters(string $hook, mixed $value, mixed ...$args): mixed
    {
        foreach ($this->sortedCallbacks($this->filters[$hook] ?? []) as $callback) {
            $value = $callback($value, ...$args);
        }

        return $value;
    }

    protected function sortedCallbacks(array $callbacks): array
    {
        if ($callbacks === []) {
            return [];
        }

        ksort($callbacks);

        return array_merge(...array_values($callbacks));
    }
}
