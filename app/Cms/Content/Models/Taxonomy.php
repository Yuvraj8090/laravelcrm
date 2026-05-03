<?php

namespace App\Cms\Content\Models;

use App\Core\Tenancy\Models\Concerns\BelongsToWebsite;
use App\Core\Website\Models\Website;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Taxonomy extends Model
{
    use BelongsToWebsite;

    protected $fillable = [
        'website_id',
        'type',
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_taxonomy');
    }
}
