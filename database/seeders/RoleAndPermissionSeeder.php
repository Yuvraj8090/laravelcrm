<?php

namespace Database\Seeders;

use App\Core\Website\Models\Permission;
use App\Core\Website\Models\Role;
use App\Core\Website\Models\Website;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $website = Website::query()->firstOrFail();

        $permissions = collect([
            ['name' => 'Manage websites', 'slug' => 'websites.manage', 'group_name' => 'websites'],
            ['name' => 'Manage content', 'slug' => 'contents.manage', 'group_name' => 'contents'],
            ['name' => 'Publish content', 'slug' => 'contents.publish', 'group_name' => 'contents'],
            ['name' => 'Manage themes', 'slug' => 'themes.manage', 'group_name' => 'appearance'],
            ['name' => 'Manage plugins', 'slug' => 'plugins.manage', 'group_name' => 'appearance'],
            ['name' => 'Manage media', 'slug' => 'media.manage', 'group_name' => 'contents'],
        ])->map(fn ($permission) => Permission::query()->updateOrCreate(['slug' => $permission['slug']], $permission));

        $roles = [
            'admin' => ['Admin', $permissions->pluck('id')->all()],
            'editor' => ['Editor', $permissions->whereIn('slug', ['contents.manage', 'contents.publish', 'media.manage'])->pluck('id')->all()],
            'author' => ['Author', $permissions->whereIn('slug', ['contents.manage', 'media.manage'])->pluck('id')->all()],
            'subscriber' => ['Subscriber', []],
        ];

        foreach ($roles as $slug => [$name, $permissionIds]) {
            $role = Role::query()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => $slug],
                ['name' => $name, 'is_system' => true]
            );

            $role->permissions()->sync($permissionIds);
        }
    }
}
