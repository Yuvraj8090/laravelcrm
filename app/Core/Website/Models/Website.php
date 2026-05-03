<?php

namespace App\Core\Website\Models;

use App\Cms\Content\Models\Content;
use App\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'primary_domain',
        'theme_slug',
        'locale',
        'timezone',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function domains(): HasMany
    {
        return $this->hasMany(WebsiteDomain::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function siteSettings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function plugins(): HasMany
    {
        return $this->hasMany(WebsitePlugin::class);
    }
}
