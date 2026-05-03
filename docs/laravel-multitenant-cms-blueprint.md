# Laravel Multi-Tenant CMS Blueprint

## 1. Goal

Build a Laravel-based CMS that behaves like a WordPress-style platform, but is designed from the start for:

- Multi-tenancy
- Theme switching
- Plugin extensibility
- Centralized administration
- Good performance at scale

This blueprint assumes a single Laravel codebase serving many websites such as:

- Schools
- Company websites
- Blogs
- Marketing sites
- Small e-commerce stores

## 2. High-Level Architecture

### 2.1 Core layers

Use a modular monolith first. It is easier to ship and maintain than microservices, while still allowing strict separation of concerns.

Recommended top-level domains:

- `Core`: tenancy, website lifecycle, shared kernel
- `Cms`: pages, posts, categories, menus, media, comments
- `Themes`: theme registry, rendering, settings, asset resolution
- `Plugins`: plugin registry, hook system, lifecycle management
- `Users`: users, roles, permissions, invitations
- `Settings`: system settings, site settings, theme settings, plugin settings
- `Commerce`: optional module, can be enabled by plugin/package later
- `Api`: REST or GraphQL access for core-theme-plugin communication

### 2.2 Multi-tenant model

Use a central database plus a tenant-aware data model.

Two viable patterns:

1. Shared database, shared schema, row-level tenancy
2. Central database + per-tenant database

Recommended starting point:

- Use a central database for platform-level data
- Use shared-schema row-level tenancy for CMS content
- Add optional per-tenant database support later for large enterprise tenants

Reason:

- Easier provisioning
- Easier maintenance
- Faster local development
- Lower operational complexity early on

Every tenant-owned table should include `website_id`.

### 2.3 Request resolution

Each incoming request should resolve the active website by:

- Custom domain, or
- Subdomain, or
- Admin-selected preview context

Flow:

1. `IdentifyWebsite` middleware resolves website from host
2. `WebsiteContext` is bound into the container
3. Global scopes or tenant-aware repositories constrain data access
4. Theme engine renders the active theme for that website
5. Active plugins for that website are booted

## 3. Recommended Folder Structure

```text
app/
  Core/
    Tenancy/
      Models/
      Middleware/
      Services/
      Context/
    Website/
      Models/
      Actions/
      Services/
    Support/
      Hooks/
      Contracts/
      Exceptions/

  Cms/
    Content/
      Models/
      Actions/
      Policies/
      Services/
    Taxonomy/
      Models/
      Services/
    Media/
      Models/
      Jobs/
      Services/
    Navigation/
      Models/
      Services/

  Themes/
    Contracts/
    DTOs/
    Services/
    Registries/
    Exceptions/

  Plugins/
    Contracts/
    DTOs/
    Services/
    Registries/
    Console/
    Support/

  Users/
    Models/
    Policies/
    Services/

  Settings/
    Models/
    Services/
    Repositories/

bootstrap/
config/
database/
  migrations/
  seeders/

modules/
  plugins/
    SeoToolkit/
      plugin.json
      src/
        PluginServiceProvider.php
        Hooks/
        Http/
        Models/
      routes/
        web.php
        api.php
      database/
        migrations/
      resources/
        views/
      public/
      config/
        plugin.php

  themes/
    academy/
      theme.json
      resources/
        views/
          layouts/
          pages/
          partials/
      public/
        css/
        js/
        images/
      config/
        settings.php
      src/
        ThemeServiceProvider.php

resources/
  views/
    admin/
    system/
routes/
  web.php
  api.php
```

## 4. Database Schema

## 4.1 Platform tables

### `websites`

```sql
id BIGINT UNSIGNED PRIMARY KEY
name VARCHAR(150)
slug VARCHAR(150) UNIQUE
status VARCHAR(30) INDEX
primary_domain VARCHAR(255) NULL UNIQUE
theme_slug VARCHAR(120) NULL
locale VARCHAR(10) DEFAULT 'en'
timezone VARCHAR(64) DEFAULT 'UTC'
settings JSON NULL
created_at TIMESTAMP
updated_at TIMESTAMP
deleted_at TIMESTAMP NULL
```

### `website_domains`

```sql
id BIGINT UNSIGNED PRIMARY KEY
website_id BIGINT UNSIGNED INDEX
domain VARCHAR(255) UNIQUE
is_primary BOOLEAN DEFAULT 0
ssl_status VARCHAR(30) NULL
created_at TIMESTAMP
updated_at TIMESTAMP
```

### `website_users`

```sql
id BIGINT UNSIGNED PRIMARY KEY
website_id BIGINT UNSIGNED INDEX
user_id BIGINT UNSIGNED INDEX
role_id BIGINT UNSIGNED INDEX
created_at TIMESTAMP
updated_at TIMESTAMP
UNIQUE KEY website_user_unique (website_id, user_id)
```

### `roles`

```sql
id BIGINT UNSIGNED PRIMARY KEY
website_id BIGINT UNSIGNED NULL INDEX
name VARCHAR(100)
slug VARCHAR(100)
is_system BOOLEAN DEFAULT 0
created_at TIMESTAMP
updated_at TIMESTAMP
UNIQUE KEY website_role_slug_unique (website_id, slug)
```

### `permissions`

```sql
id BIGINT UNSIGNED PRIMARY KEY
name VARCHAR(120)
slug VARCHAR(120) UNIQUE
group_name VARCHAR(120)
created_at TIMESTAMP
updated_at TIMESTAMP
```

### `role_permissions`

```sql
role_id BIGINT UNSIGNED INDEX
permission_id BIGINT UNSIGNED INDEX
PRIMARY KEY (role_id, permission_id)
```

### `installed_themes`

```sql
id BIGINT UNSIGNED PRIMARY KEY
slug VARCHAR(120) UNIQUE
name VARCHAR(150)
version VARCHAR(40)
author VARCHAR(150) NULL
description TEXT NULL
path VARCHAR(255)
is_enabled BOOLEAN DEFAULT 1
manifest JSON
created_at TIMESTAMP
updated_at TIMESTAMP
```

### `installed_plugins`

```sql
id BIGINT UNSIGNED PRIMARY KEY
slug VARCHAR(120) UNIQUE
name VARCHAR(150)
version VARCHAR(40)
author VARCHAR(150) NULL
description TEXT NULL
path VARCHAR(255)
is_enabled BOOLEAN DEFAULT 1
manifest JSON
created_at TIMESTAMP
updated_at TIMESTAMP
```

### `website_plugins`

```sql
website_id BIGINT UNSIGNED INDEX
plugin_slug VARCHAR(120) INDEX
is_active BOOLEAN DEFAULT 1
settings JSON NULL
activated_at TIMESTAMP NULL
PRIMARY KEY (website_id, plugin_slug)
```

### `settings`

```sql
id BIGINT UNSIGNED PRIMARY KEY
website_id BIGINT UNSIGNED NULL INDEX
group_name VARCHAR(120)
key_name VARCHAR(160)
value JSON NULL
autoload BOOLEAN DEFAULT 0
created_at TIMESTAMP
updated_at TIMESTAMP
UNIQUE KEY setting_scope_unique (website_id, group_name, key_name)
INDEX settings_autoload_idx (website_id, autoload)
```

## 4.2 CMS content tables

### `contents`

Use one table for posts, pages, custom post types.

```sql
id BIGINT UNSIGNED PRIMARY KEY
website_id BIGINT UNSIGNED INDEX
type VARCHAR(60) INDEX
status VARCHAR(30) INDEX
author_id BIGINT UNSIGNED INDEX
slug VARCHAR(190)
title VARCHAR(255)
excerpt TEXT NULL
body LONGTEXT
meta_title VARCHAR(255) NULL
meta_description TEXT NULL
published_at TIMESTAMP NULL
created_at TIMESTAMP
updated_at TIMESTAMP
deleted_at TIMESTAMP NULL
UNIQUE KEY website_type_slug_unique (website_id, type, slug)
INDEX content_listing_idx (website_id, type, status, published_at)
```

### `content_meta`

```sql
id BIGINT UNSIGNED PRIMARY KEY
content_id BIGINT UNSIGNED INDEX
key_name VARCHAR(160)
value JSON NULL
created_at TIMESTAMP
updated_at TIMESTAMP
UNIQUE KEY content_meta_unique (content_id, key_name)
```

### `taxonomies`

```sql
id BIGINT UNSIGNED PRIMARY KEY
website_id BIGINT UNSIGNED INDEX
type VARCHAR(60) INDEX
name VARCHAR(150)
slug VARCHAR(190)
description TEXT NULL
parent_id BIGINT UNSIGNED NULL INDEX
created_at TIMESTAMP
updated_at TIMESTAMP
UNIQUE KEY website_taxonomy_slug_unique (website_id, type, slug)
```

### `content_taxonomy`

```sql
content_id BIGINT UNSIGNED INDEX
taxonomy_id BIGINT UNSIGNED INDEX
PRIMARY KEY (content_id, taxonomy_id)
```

### `media`

```sql
id BIGINT UNSIGNED PRIMARY KEY
website_id BIGINT UNSIGNED INDEX
uploaded_by BIGINT UNSIGNED INDEX
disk VARCHAR(50)
path VARCHAR(255)
filename VARCHAR(255)
mime_type VARCHAR(120)
size BIGINT UNSIGNED
width INT NULL
height INT NULL
alt_text VARCHAR(255) NULL
meta JSON NULL
created_at TIMESTAMP
updated_at TIMESTAMP
INDEX media_lookup_idx (website_id, mime_type, created_at)
```

### `menus`

```sql
id BIGINT UNSIGNED PRIMARY KEY
website_id BIGINT UNSIGNED INDEX
name VARCHAR(120)
location VARCHAR(120) NULL
created_at TIMESTAMP
updated_at TIMESTAMP
```

### `menu_items`

```sql
id BIGINT UNSIGNED PRIMARY KEY
menu_id BIGINT UNSIGNED INDEX
parent_id BIGINT UNSIGNED NULL INDEX
title VARCHAR(150)
url VARCHAR(255) NULL
target VARCHAR(20) NULL
sort_order INT DEFAULT 0
reference_type VARCHAR(60) NULL
reference_id BIGINT UNSIGNED NULL
created_at TIMESTAMP
updated_at TIMESTAMP
INDEX menu_sort_idx (menu_id, parent_id, sort_order)
```

## 4.3 Audit and background processing

### `activity_logs`

```sql
id BIGINT UNSIGNED PRIMARY KEY
website_id BIGINT UNSIGNED NULL INDEX
user_id BIGINT UNSIGNED NULL INDEX
action VARCHAR(120)
subject_type VARCHAR(120)
subject_id BIGINT UNSIGNED NULL
properties JSON NULL
created_at TIMESTAMP
INDEX activity_scope_idx (website_id, action, created_at)
```

Use standard Laravel tables too:

- `jobs`
- `failed_jobs`
- `cache`
- `sessions`
- `password_reset_tokens`

## 5. Tenancy Design

### 5.1 Website context object

```php
<?php

namespace App\Core\Tenancy\Context;

use App\Core\Website\Models\Website;

class WebsiteContext
{
    public function __construct(
        protected ?Website $website = null
    ) {}

    public function set(Website $website): void
    {
        $this->website = $website;
    }

    public function get(): ?Website
    {
        return $this->website;
    }

    public function id(): ?int
    {
        return $this->website?->id;
    }

    public function hasWebsite(): bool
    {
        return $this->website !== null;
    }
}
```

### 5.2 Domain resolver middleware

```php
<?php

namespace App\Core\Tenancy\Middleware;

use App\Core\Tenancy\Context\WebsiteContext;
use App\Core\Website\Models\Website;
use Closure;
use Illuminate\Http\Request;

class IdentifyWebsite
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        $website = Website::query()
            ->where('primary_domain', $host)
            ->orWhereHas('domains', fn ($query) => $query->where('domain', $host))
            ->firstOrFail();

        app(WebsiteContext::class)->set($website);

        return $next($request);
    }
}
```

### 5.3 Tenant-aware model trait

```php
<?php

namespace App\Core\Tenancy\Models\Concerns;

use App\Core\Tenancy\Context\WebsiteContext;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToWebsite
{
    protected static function bootBelongsToWebsite(): void
    {
        static::creating(function ($model) {
            if (!$model->website_id && app(WebsiteContext::class)->hasWebsite()) {
                $model->website_id = app(WebsiteContext::class)->id();
            }
        });

        static::addGlobalScope('website', function (Builder $builder) {
            $websiteId = app(WebsiteContext::class)->id();

            if ($websiteId) {
                $builder->where($builder->getModel()->getTable() . '.website_id', $websiteId);
            }
        });
    }
}
```

Note:

- Do not apply this global scope in platform admin screens
- Use admin-side repositories or explicit `withoutGlobalScope('website')`

## 6. Theme System

## 6.1 Theme manifest

Each theme should ship with a `theme.json`.

```json
{
  "name": "Academy",
  "slug": "academy",
  "version": "1.0.0",
  "author": "Your Team",
  "description": "A school-focused responsive theme.",
  "main_provider": "Themes\\\\Academy\\\\ThemeServiceProvider",
  "view_namespace": "theme-academy",
  "supports": {
    "menus": true,
    "hero_blocks": true,
    "customizer": true
  },
  "settings": {
    "colors.primary": "#0f4c81",
    "homepage.layout": "school"
  }
}
```

## 6.2 Theme contract

```php
<?php

namespace App\Themes\Contracts;

interface ThemeInterface
{
    public function slug(): string;

    public function boot(): void;

    public function registerViewPaths(): void;

    public function registerAssets(): void;

    public function settingsSchema(): array;
}
```

## 6.3 Theme registry

```php
<?php

namespace App\Themes\Registries;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ThemeRegistry
{
    public function all(): Collection
    {
        return collect(File::directories(base_path('modules/themes')))
            ->map(function (string $path) {
                $manifestPath = $path . '/theme.json';

                if (!File::exists($manifestPath)) {
                    return null;
                }

                $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

                return array_merge($manifest, ['path' => $path]);
            })
            ->filter()
            ->values();
    }

    public function find(string $slug): ?array
    {
        return $this->all()->firstWhere('slug', $slug);
    }
}
```

## 6.4 Theme manager

```php
<?php

namespace App\Themes\Services;

use App\Core\Tenancy\Context\WebsiteContext;
use App\Themes\Registries\ThemeRegistry;
use Illuminate\Support\Facades\View;

class ThemeManager
{
    public function __construct(
        protected ThemeRegistry $themes,
        protected WebsiteContext $context
    ) {}

    public function active(): ?array
    {
        $website = $this->context->get();

        if (!$website || !$website->theme_slug) {
            return null;
        }

        return $this->themes->find($website->theme_slug);
    }

    public function bootActiveTheme(): void
    {
        $theme = $this->active();

        if (!$theme) {
            return;
        }

        View::addNamespace(
            $theme['view_namespace'],
            $theme['path'] . '/resources/views'
        );
    }

    public function view(string $template, array $data = [])
    {
        $theme = $this->active();

        abort_if(!$theme, 500, 'No active theme configured.');

        return view($theme['view_namespace'] . '::' . $template, $data);
    }
}
```

## 6.5 Theme-specific settings

Store theme settings in `settings` using:

- `group_name = theme:{slug}`
- `website_id = current website`

Example keys:

- `theme:academy.colors.primary`
- `theme:academy.header.logo`
- `theme:academy.homepage.layout`

Best practice:

- Store only overrides per website
- Merge with theme defaults from `theme.json` or `config/settings.php`

## 7. Plugin System

## 7.1 Plugin manifest

Each plugin should ship with `plugin.json`.

```json
{
  "name": "SEO Toolkit",
  "slug": "seo-toolkit",
  "version": "1.0.0",
  "author": "Your Team",
  "description": "SEO metadata, sitemap and schema tools.",
  "main_provider": "Plugins\\\\SeoToolkit\\\\PluginServiceProvider",
  "requires": {
    "php": "^8.3",
    "laravel": "^12.0"
  }
}
```

## 7.2 Hook engine

You want WordPress-like actions and filters, but with typed Laravel-friendly internals.

### Hook manager

```php
<?php

namespace App\Core\Support\Hooks;

class HookManager
{
    protected array $actions = [];
    protected array $filters = [];

    public function addAction(string $hook, callable $callback, int $priority = 10): void
    {
        $this->actions[$hook][$priority][] = $callback;
    }

    public function doAction(string $hook, mixed ...$args): void
    {
        foreach ($this->sortedCallbacks($this->actions[$hook] ?? []) as $callback) {
            $callback(...$args);
        }
    }

    public function addFilter(string $hook, callable $callback, int $priority = 10): void
    {
        $this->filters[$hook][$priority][] = $callback;
    }

    public function applyFilters(string $hook, mixed $value, mixed ...$args): mixed
    {
        foreach ($this->sortedCallbacks($this->filters[$hook] ?? []) as $callback) {
            $value = $callback($value, ...$args);
        }

        return $value;
    }

    protected function sortedCallbacks(array $callbacks): array
    {
        ksort($callbacks);

        return array_merge(...array_values($callbacks ?: [[]]));
    }
}
```

Bind it as a singleton:

```php
$this->app->singleton(HookManager::class, fn () => new HookManager());
```

### Helper functions

```php
<?php

use App\Core\Support\Hooks\HookManager;

function cms_action(string $hook, callable $callback, int $priority = 10): void
{
    app(HookManager::class)->addAction($hook, $callback, $priority);
}

function cms_do_action(string $hook, mixed ...$args): void
{
    app(HookManager::class)->doAction($hook, ...$args);
}

function cms_filter(string $hook, callable $callback, int $priority = 10): void
{
    app(HookManager::class)->addFilter($hook, $callback, $priority);
}

function cms_apply_filters(string $hook, mixed $value, mixed ...$args): mixed
{
    return app(HookManager::class)->applyFilters($hook, $value, ...$args);
}
```

## 7.3 Plugin loader

```php
<?php

namespace App\Plugins\Services;

use App\Core\Tenancy\Context\WebsiteContext;
use App\Plugins\Registries\PluginRegistry;
use App\Core\Website\Models\WebsitePlugin;

class PluginManager
{
    public function __construct(
        protected PluginRegistry $plugins,
        protected WebsiteContext $context
    ) {}

    public function activeForCurrentWebsite(): array
    {
        $websiteId = $this->context->id();

        if (!$websiteId) {
            return [];
        }

        $activeSlugs = WebsitePlugin::query()
            ->where('website_id', $websiteId)
            ->where('is_active', true)
            ->pluck('plugin_slug')
            ->all();

        return $this->plugins
            ->all()
            ->whereIn('slug', $activeSlugs)
            ->values()
            ->all();
    }
}
```

## 7.4 Registering plugin routes, views, menus

```php
<?php

namespace Plugins\SeoToolkit;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::addNamespace('plugin-seo', __DIR__ . '/../resources/views');

        Route::middleware(['web', 'auth', 'admin'])
            ->prefix('admin/seo')
            ->group(__DIR__ . '/../routes/web.php');

        cms_action('admin.menu', function (array &$items) {
            $items[] = [
                'label' => 'SEO Toolkit',
                'icon' => 'heroicon-o-magnifying-glass',
                'route' => 'plugins.seo.index',
                'permission' => 'plugins.seo.manage',
            ];
        });

        cms_filter('content.saving', function (array $payload) {
            $payload['meta_title'] ??= $payload['title'] ?? null;
            return $payload;
        });
    }
}
```

## 7.5 Plugin migrations

Each plugin should own its own migration folder:

- `modules/plugins/PluginName/database/migrations`

Create an install action that runs plugin migrations only once.

```php
<?php

namespace App\Plugins\Services;

use Illuminate\Support\Facades\Artisan;

class PluginInstaller
{
    public function runMigrations(string $pluginPath): void
    {
        Artisan::call('migrate', [
            '--path' => str_replace(base_path() . '/', '', $pluginPath . '/database/migrations'),
            '--force' => true,
        ]);
    }
}
```

For stronger isolation, also add a table:

- `plugin_migrations`

This lets you track plugin-specific migration state separately from core.

## 8. Website Provisioning Flow

When an admin creates a new website:

1. Create `websites` row
2. Create primary domain
3. Seed default roles
4. Seed default settings
5. Assign starter theme
6. Activate default plugins
7. Queue background setup tasks

Recommended implementation:

- Action class: `CreateWebsite`
- Job chain for heavy work

Example:

```php
<?php

namespace App\Core\Website\Actions;

use App\Core\Website\Models\Website;
use App\Core\Website\Models\WebsiteDomain;
use Illuminate\Support\Facades\DB;

class CreateWebsite
{
    public function execute(array $data): Website
    {
        return DB::transaction(function () use ($data) {
            $website = Website::query()->create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'status' => 'active',
                'primary_domain' => $data['primary_domain'],
                'theme_slug' => $data['theme_slug'] ?? 'academy',
                'locale' => $data['locale'] ?? 'en',
                'timezone' => $data['timezone'] ?? 'UTC',
            ]);

            WebsiteDomain::query()->create([
                'website_id' => $website->id,
                'domain' => $data['primary_domain'],
                'is_primary' => true,
            ]);

            dispatch(new \App\Core\Website\Jobs\ProvisionWebsiteDefaults($website->id));

            return $website;
        });
    }
}
```

## 9. Admin Panel Structure

Use one central admin panel with two contexts:

- Platform admin
- Website admin

### Platform admin can manage

- Websites
- Domains
- Installed themes
- Installed plugins
- Global settings
- Queue and cache health
- Billing

### Website admin can manage

- Pages, posts, categories
- Media
- Menus
- Users in that website
- Website settings
- Theme customization
- Plugin settings

Best practice:

- Keep platform admin routes under `/platform`
- Keep website admin routes under `/admin`
- Switch website context explicitly in platform screens

## 10. API Design

Use REST first unless a specific front-end needs GraphQL.

Suggested internal API areas:

- `/api/platform/websites`
- `/api/admin/contents`
- `/api/admin/media`
- `/api/admin/settings`
- `/api/admin/themes`
- `/api/admin/plugins`

Use the API for:

- Headless admin widgets
- Theme preview
- Plugin settings screens
- Third-party integrations

Example plugin-facing contract:

```php
<?php

namespace App\Plugins\Contracts;

interface PluginBootable
{
    public function register(): void;

    public function boot(): void;
}
```

## 11. Performance Strategy

## 11.1 Database efficiency

Rules:

- Index all tenant lookups with `website_id`
- Add composite indexes for common list queries
- Use `with()` on listing screens
- Use `withCount()` instead of loading full relationships unnecessarily
- Prefer cursor pagination for heavy admin lists

Example:

```php
$posts = Content::query()
    ->where('type', 'post')
    ->where('status', 'published')
    ->with(['author', 'taxonomies'])
    ->latest('published_at')
    ->paginate(20);
```

Avoid:

- Loading media, author, taxonomies separately in loops
- Querying settings row-by-row

## 11.2 Settings cache

Cache tenant settings aggressively.

```php
<?php

namespace App\Settings\Services;

use Illuminate\Support\Facades\Cache;

class SettingsRepository
{
    public function allForWebsite(int $websiteId): array
    {
        return Cache::remember(
            "website:{$websiteId}:settings",
            now()->addHour(),
            fn () => \App\Settings\Models\Setting::query()
                ->where('website_id', $websiteId)
                ->pluck('value', 'key_name')
                ->toArray()
        );
    }
}
```

Invalidate cache on:

- Website setting update
- Theme switch
- Plugin activation/deactivation

## 11.3 View and route caching

In production:

- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`

If plugin or theme routes are dynamic, rebuild route cache during deployment or install workflows.

## 11.4 Asset optimization

Use Vite for:

- Code splitting
- Cache-busted assets
- Minification

Theme best practices:

- Build assets per theme
- Load only the active theme bundle
- Lazy-load image-heavy sections
- Use responsive images with generated sizes

Media best practices:

- Queue thumbnails and WebP/AVIF conversions
- Store dimensions on upload
- Serve CDN-backed public assets when possible

## 11.5 Queue heavy tasks

Queue the following:

- Image optimization
- Theme asset compilation
- Plugin installation checks
- Sitemap generation
- Search indexing
- Bulk imports
- Site provisioning

## 11.6 Prevent N+1 across plugins

Plugins are a common source of hidden performance issues.

Guardrails:

- Publish plugin development guidelines
- Add query count assertions in tests
- Expose a developer debug panel in non-production
- Prefer DTOs or service-layer extension points over raw model loops

## 12. Security Model

Must-have protections:

- Laravel validation on all input
- Eloquent or query builder only, avoid raw SQL unless parameterized
- CSRF protection for admin forms
- Authorization policies for every content/admin action
- Output escaping in Blade by default
- Sanitization for rich text content before render
- Signed URLs for sensitive operations
- Rate limiting on login, API, upload, and install endpoints
- File upload MIME and extension validation
- Restrict plugin/theme install permissions to super admins

Recommended additions:

- `spatie/laravel-permission` if you do not want to build RBAC yourself
- HTML purification for WYSIWYG content
- Audit logs for admin actions
- Two-factor authentication for platform admins

## 13. Lightweight and Performant by Design

To keep the platform lean:

### Use a modular monolith, not a package explosion

- Keep core modules inside `app/`
- Use `modules/plugins` and `modules/themes` for extension loading
- Introduce separate Composer packages only when code is truly reusable outside this CMS

### Keep plugin APIs narrow

Allow plugins to extend through:

- Hooks
- Service contracts
- Route registration
- Migrations
- Admin menu injection

Do not let plugins freely mutate container bindings without conventions.

### Prefer capabilities over arbitrary code execution

Good plugin extension points:

- Register custom content type
- Register settings tab
- Register admin widget
- Register frontend block
- Filter render output

This is safer and easier to optimize than unconstrained plugin behavior.

### Cache at the website boundary

Examples:

- Website settings cache
- Active theme cache
- Active plugin manifest cache
- Rendered navigation cache
- Homepage fragment cache

### Keep admin and frontend separate

- Frontend rendering should never load heavy admin-only services
- Use separate middleware groups and service providers where helpful

## 14. Recommended Build Order

Build in this sequence:

1. Base Laravel app with auth, queue, cache, policies
2. Website and domain resolution
3. Core CMS content model
4. Central admin panel
5. Theme registry and active-theme rendering
6. Plugin registry and hook engine
7. Website provisioning
8. Theme customization settings
9. Plugin migrations/routes/admin menu integration
10. Performance hardening and developer tooling

## 15. Minimal Viable Plugin Lifecycle

States:

- Installed
- Enabled globally
- Activated for website
- Deactivated for website
- Deleted

Operations:

- Install reads `plugin.json`, stores manifest, optionally runs migrations
- Activate for website creates `website_plugins` row
- Boot only plugins active for the current website
- Deactivate removes hooks from runtime by not booting that plugin on subsequent requests

## 16. Minimal Viable Theme Lifecycle

States:

- Installed
- Enabled
- Selected by website
- Previewing

Operations:

- Install reads `theme.json`
- Save manifest in `installed_themes`
- Select by updating `websites.theme_slug`
- Load theme views/assets/settings during website request boot

## 17. Suggested Packages

Use Laravel core first, then add only what earns its cost.

Good candidates:

- `laravel/horizon` for queues
- `laravel/scout` if site search becomes important
- `spatie/laravel-permission` for RBAC
- `spatie/laravel-medialibrary` only if you want faster media delivery features without building them all
- `stancl/tenancy` only if you choose to accelerate tenancy infrastructure rather than writing your own

If your goal is true “from scratch” architecture learning, keep tenancy and plugin/theme orchestration custom, but use stable packages for generic concerns like RBAC and media if timelines matter.

## 18. Practical Recommendation

For this project, I would implement:

- Custom website context and request resolution
- Custom theme registry and theme manager
- Custom plugin registry and hook system
- Shared-schema multi-tenancy via `website_id`
- Central admin + per-website admin separation
- REST API for internal extensibility
- Aggressive caching for settings, menus, and theme/plugin manifests

I would avoid at first:

- Per-tenant databases
- Arbitrary plugin marketplace uploads
- Dynamic code execution from untrusted packages
- Overly generic block builders before the core CMS is stable

## 19. Next Implementation Step

The best first coding milestone is:

- Bootstrap a fresh Laravel app
- Add `websites`, `website_domains`, `settings`, `contents`, `taxonomies`, `installed_themes`, `installed_plugins`, and `website_plugins` migrations
- Implement `WebsiteContext`, `IdentifyWebsite`, `ThemeRegistry`, `ThemeManager`, `PluginRegistry`, `PluginManager`, and `HookManager`
- Build one sample theme and one sample plugin to prove the extension model

Once that works, the rest of the CMS becomes an iterative product build instead of a risky architecture experiment.
