<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_homepage_loads(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Laravel CMS Platform')
            ->assertSee('starter-site/preview', false);
    }

    public function test_seeded_site_can_be_previewed_through_the_theme_manager(): void
    {
        $this->seed();

        $response = $this->get('/sites/starter-site/preview');

        $response
            ->assertOk()
            ->assertSee('Starter Site')
            ->assertSee('Starter Theme');
    }
}
