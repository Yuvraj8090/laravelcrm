<?php

namespace App\Cms\Content\Models;

use App\Core\Tenancy\Models\Concerns\BelongsToWebsite;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use BelongsToWebsite;

    protected $fillable = [
        'website_id',
        'name',
        'area',
        'type',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];
}
