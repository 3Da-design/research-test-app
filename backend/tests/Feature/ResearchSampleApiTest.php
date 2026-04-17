<?php

namespace Tests\Feature;

use Database\Seeders\ResearchSampleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResearchSampleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ResearchSampleSeeder::class);
    }

    public function test_api_hello_returns_message_from_database(): void
    {
        $response = $this->getJson('/api/hello');

        $response->assertOk()
            ->assertJsonPath('message', 'Hello from shared Laravel backend API');
    }

    public function test_api_items_returns_rows_from_database(): void
    {
        $response = $this->getJson('/api/items');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.name', 'Laravel')
            ->assertJsonPath('data.1.name', 'React')
            ->assertJsonPath('data.2.name', 'Comparison Sample');
    }

    public function test_blade_hello_renders_database_content(): void
    {
        $response = $this->get('/blade/hello');

        $response->assertOk();
        $response->assertSee('Hello from shared Laravel backend API', false);
        $response->assertSee('Laravel', false);
        $response->assertSee('Comparison Sample', false);
    }
}
