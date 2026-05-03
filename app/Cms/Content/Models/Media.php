<?php

namespace App\Cms\Content\Models;

use App\Core\Tenancy\Models\Concerns\BelongsToWebsite;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use BelongsToWebsite;

    protected $fillable = [
        'website_id',
        'uploaded_by',
        'disk',
        'path',
        'filename',
        'mime_type',
        'size',
        'width',
        'height',
        'alt_text',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
