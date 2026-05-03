<?php

namespace App\Support\Services;

use App\Core\Support\Models\ActivityLog;
use App\Core\Tenancy\Context\WebsiteContext;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public function __construct(
        protected WebsiteContext $context
    ) {
    }

    public function log(string $action, ?Model $subject = null, array $properties = []): void
    {
        ActivityLog::query()->create([
            'website_id' => $this->context->id(),
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties,
        ]);
    }
}
