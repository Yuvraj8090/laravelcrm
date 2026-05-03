<?php

namespace App\Themes\Registries;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ThemeRegistry
{
    public function __construct(
        protected string $path
    ) {
    }

    public function all(): Collection
    {
        if (!File::isDirectory($this->path)) {
            return collect();
        }

        return collect(File::directories($this->path))
            ->map(function (string $themePath) {
                $manifestPath = $themePath.'/theme.json';

                if (!File::exists($manifestPath)) {
                    return null;
                }

                $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

                return array_merge($manifest, ['path' => $themePath]);
            })
            ->filter()
            ->values();
    }

    public function find(string $slug): ?array
    {
        return $this->all()->firstWhere('slug', $slug);
    }
}
