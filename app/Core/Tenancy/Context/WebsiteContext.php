<?php

namespace App\Core\Tenancy\Context;

use App\Core\Website\Models\Website;

class WebsiteContext
{
    public function __construct(
        protected ?Website $website = null
    ) {
    }

    public function set(Website $website): void
    {
        $this->website = $website;
    }

    public function get(): ?Website
    {
        return $this->website;
    }

    public function id(): ?int
    {
        return $this->website?->getKey();
    }

    public function hasWebsite(): bool
    {
        return $this->website !== null;
    }
}
