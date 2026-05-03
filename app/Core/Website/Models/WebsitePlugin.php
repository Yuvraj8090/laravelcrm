<?php

namespace App\Core\Website\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsitePlugin extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'website_id',
        'plugin_slug',
        'is_active',
        'settings',
        'activated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'activated_at' => 'datetime',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
