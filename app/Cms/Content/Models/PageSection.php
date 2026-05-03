<?php

namespace App\Cms\Content\Models;

use App\Core\Tenancy\Models\Concerns\BelongsToWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    use BelongsToWebsite;

    protected $fillable = [
        'website_id',
        'content_id',
        'name',
        'type',
        'sort_order',
        'settings',
        'is_reusable',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_reusable' => 'boolean',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
