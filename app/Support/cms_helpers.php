<?php

use App\Core\Support\Hooks\HookManager;

if (!function_exists('cms_action')) {
    function cms_action(string $hook, callable $callback, int $priority = 10): void
    {
        app(HookManager::class)->addAction($hook, $callback, $priority);
    }
}

if (!function_exists('cms_do_action')) {
    function cms_do_action(string $hook, mixed ...$args): void
    {
        app(HookManager::class)->doAction($hook, ...$args);
    }
}

if (!function_exists('cms_filter')) {
    function cms_filter(string $hook, callable $callback, int $priority = 10): void
    {
        app(HookManager::class)->addFilter($hook, $callback, $priority);
    }
}

if (!function_exists('cms_apply_filters')) {
    function cms_apply_filters(string $hook, mixed $value, mixed ...$args): mixed
    {
        return app(HookManager::class)->applyFilters($hook, $value, ...$args);
    }
}
