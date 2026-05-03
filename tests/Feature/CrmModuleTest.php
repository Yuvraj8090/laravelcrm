<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_crm_login_page_loads(): void
    {
        $response = $this->get('/crm/login');

        $response
            ->assertOk()
            ->assertSee('Welcome to the CRM');
    }

    public function test_seeded_crm_admin_can_login_and_view_dashboard(): void
    {
        $this->seed();

        $response = $this->post('/crm/login', [
            'email' => 'crm.admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('crm.dashboard'));

        $dashboard = $this->get(route('crm.dashboard'));

        $dashboard
            ->assertOk()
            ->assertSee('CRM Dashboard')
            ->assertSee('Pipeline Performance');
    }

    public function test_crm_user_can_update_theme_settings(): void
    {
        $this->seed();
        $user = User::query()->where('email', 'crm.admin@example.com')->firstOrFail();

        $response = $this->actingAs($user)->put(route('crm.settings.update'), [
            'crm_theme' => 'brand',
            'crm_theme_settings' => [
                'logo_text' => 'Yashi CRM',
                'primary_color' => '#4f46e5',
                'secondary_color' => '#ec4899',
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'crm_theme' => 'brand',
        ]);
    }
}
