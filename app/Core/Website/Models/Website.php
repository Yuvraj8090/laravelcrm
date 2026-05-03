<?php

namespace App\Core\Website\Models;

use App\Cms\Content\Models\Content;
use App\Cms\Content\Models\Media;
use App\Cms\Content\Models\Menu;
use App\Cms\Content\Models\PageSection;
use App\Cms\Content\Models\Taxonomy;
use App\Cms\Content\Models\Widget;
use App\Settings\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function taxonomies(): HasMany
    {
        return $this->hasMany(Taxonomy::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(Widget::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'website_users')
            ->withPivot('role_id')
            ->withTimestamps();
    }
}
