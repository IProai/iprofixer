<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuntimeFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_available_in_light_mode(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('IProFixer')
            ->assertSee('color-scheme', false);
    }

    public function test_health_endpoint_reports_runtime_status(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok');
    }

    public function test_readiness_endpoint_can_reach_the_database(): void
    {
        $this->getJson('/ready')
            ->assertOk()
            ->assertJsonPath('status', 'ready');
    }

    public function test_arabic_locale_sets_rtl_document_direction(): void
    {
        $this->post('/locale/ar')->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('آي برو فيكسر');
    }
}
