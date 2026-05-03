<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_guests_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_seeded_site_can_be_previewed(): void
    {
        $this->seed();

        $response = $this->get('/sites/starter-site/preview');

        $response
            ->assertOk()
            ->assertSee('Starter Site')
            ->assertSee('Starter Theme');
    }

    public function test_seeded_admin_can_access_dashboard(): void
    {
        $this->seed();
        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Quick Actions');
    }

    public function test_api_login_returns_a_token(): void
    {
        $this->seed();

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);
    }
}
