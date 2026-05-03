<?php

namespace App\Cms\Content\Models;

use App\Core\Tenancy\Models\Concerns\BelongsToWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use BelongsToWebsite;

    protected $fillable = [
        'website_id',
        'name',
        'location',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }
}
