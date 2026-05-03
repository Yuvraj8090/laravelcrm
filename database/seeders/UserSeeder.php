<?php

namespace Database\Seeders;

use App\Core\Website\Models\Role;
use App\Core\Website\Models\Website;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $website = Website::query()->firstOrFail();

        $users = [
            ['name' => 'Admin User', 'username' => 'admin', 'email' => 'admin@example.com', 'password' => 'password', 'role' => 'admin'],
            ['name' => 'Editor User', 'username' => 'editor', 'email' => 'editor@example.com', 'password' => 'password', 'role' => 'editor'],
            ['name' => 'Author User', 'username' => 'author', 'email' => 'author@example.com', 'password' => 'password', 'role' => 'author'],
            ['name' => 'Subscriber User', 'username' => 'subscriber', 'email' => 'subscriber@example.com', 'password' => 'password', 'role' => 'subscriber'],
        ];

        foreach ($users as $seed) {
            $role = Role::query()->where('website_id', $website->id)->where('slug', $seed['role'])->firstOrFail();
            $user = User::query()->updateOrCreate(
                ['email' => $seed['email']],
                [
                    'name' => $seed['name'],
                    'username' => $seed['username'],
                    'password' => $seed['password'],
                    'api_token' => hash('sha256', Str::random(60)),
                ]
            );

            $website->users()->syncWithoutDetaching([$user->id => ['role_id' => $role->id]]);
        }
    }
}
