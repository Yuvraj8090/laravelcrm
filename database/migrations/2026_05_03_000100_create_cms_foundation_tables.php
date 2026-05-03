<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('status', 30)->index()->default('active');
            $table->string('primary_domain')->nullable()->unique();
            $table->string('theme_slug', 120)->nullable();
            $table->string('locale', 10)->default('en');
            $table->string('timezone', 64)->default('UTC');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('website_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->string('domain')->unique();
            $table->boolean('is_primary')->default(false);
            $table->string('ssl_status', 30)->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->nullable()->constrained('websites')->nullOnDelete();
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->boolean('is_system')->default(false);
            $table->timestamps();
            $table->unique(['website_id', 'slug']);
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 120)->unique();
            $table->string('group_name', 120)->nullable();
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('website_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['website_id', 'user_id']);
        });

        Schema::create('installed_themes', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('name', 150);
            $table->string('version', 40);
            $table->string('author', 150)->nullable();
            $table->text('description')->nullable();
            $table->string('path');
            $table->boolean('is_enabled')->default(true);
            $table->json('manifest');
            $table->timestamps();
        });

        Schema::create('installed_plugins', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('name', 150);
            $table->string('version', 40);
            $table->string('author', 150)->nullable();
            $table->text('description')->nullable();
            $table->string('path');
            $table->boolean('is_enabled')->default(true);
            $table->json('manifest');
            $table->timestamps();
        });

        Schema::create('website_plugins', function (Blueprint $table) {
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->string('plugin_slug', 120);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->primary(['website_id', 'plugin_slug']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('group_name', 120);
            $table->string('key_name', 160);
            $table->json('value')->nullable();
            $table->boolean('autoload')->default(false)->index();
            $table->timestamps();
            $table->unique(['website_id', 'group_name', 'key_name']);
        });

        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->string('type', 60)->index();
            $table->string('status', 30)->index()->default('draft');
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('slug', 190);
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['website_id', 'type', 'slug']);
            $table->index(['website_id', 'type', 'status', 'published_at']);
        });

        Schema::create('content_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->string('key_name', 160);
            $table->json('value')->nullable();
            $table->timestamps();
            $table->unique(['content_id', 'key_name']);
        });

        Schema::create('taxonomies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->string('type', 60)->index();
            $table->string('name', 150);
            $table->string('slug', 190);
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('taxonomies')->nullOnDelete();
            $table->timestamps();
            $table->unique(['website_id', 'type', 'slug']);
        });

        Schema::create('content_taxonomy', function (Blueprint $table) {
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->foreignId('taxonomy_id')->constrained('taxonomies')->cascadeOnDelete();
            $table->primary(['content_id', 'taxonomy_id']);
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('disk', 50)->default('public');
            $table->string('path');
            $table->string('filename');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['website_id', 'mime_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('content_taxonomy');
        Schema::dropIfExists('taxonomies');
        Schema::dropIfExists('content_meta');
        Schema::dropIfExists('contents');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('website_plugins');
        Schema::dropIfExists('installed_plugins');
        Schema::dropIfExists('installed_themes');
        Schema::dropIfExists('website_users');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('website_domains');
        Schema::dropIfExists('websites');
    }
};
