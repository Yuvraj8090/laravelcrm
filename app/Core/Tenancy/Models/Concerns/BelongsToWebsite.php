<?php

namespace App\Core\Tenancy\Models\Concerns;

use App\Core\Tenancy\Context\WebsiteContext;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToWebsite
{
    protected static function bootBelongsToWebsite(): void
    {
        static::creating(function ($model): void {
            $context = app(WebsiteContext::class);

            if (!$model->website_id && $context->hasWebsite()) {
                $model->website_id = $context->id();
            }
        });

        static::addGlobalScope('website', function (Builder $builder): void {
            $context = app(WebsiteContext::class);

            if ($context->hasWebsite()) {
                $builder->where(
                    $builder->getModel()->getTable().'.website_id',
                    $context->id()
                );
            }
        });
    }
}
