<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LanguageControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @var string
     */
    private string $api_endpoint = "/api/languages";

    /**
     * @var User
     */
    private User $test_user;

    /**
     * Setup
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->test_user = User::factory()->create();
    }

    /**
     * @return void
     */
    public function test_returning_paginated_languages(): void
    {
        $language = Language::factory()->create();

        $response = $this->actingAs($this->test_user, 'sanctum')
            ->get($this->api_endpoint);
        $responseContent = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertTrue(property_exists($responseContent, "current_page"));
        $this->assertTrue(property_exists($responseContent, "per_page"));
        $this->assertTrue(property_exists($responseContent, "total"));
        $this->assertTrue(property_exists($responseContent, "data"));
        $this->assertTrue(property_exists($responseContent, "first_page_url"));
        $this->assertTrue(property_exists($responseContent, "from"));
        $this->assertTrue(property_exists($responseContent, "last_page"));
        $this->assertTrue(property_exists($responseContent, "last_page_url"));
        $this->assertTrue(property_exists($responseContent, "links"));
        $this->assertTrue(property_exists($responseContent, "next_page_url"));
        $this->assertTrue(property_exists($responseContent, "path"));
        $this->assertTrue(property_exists($responseContent, "prev_page_url"));
        $this->assertTrue(property_exists($responseContent, "to"));

        $language->forceDelete();
    }

    /**
     * @return void
     */
    public function test_returning_filter_languages_by_code_and_name()
    {
        $language1 = Language::factory()->create(['code' => 'xx', 'name' => 'Test']);
        $language2 = Language::factory()->create();

        $response = $this->actingAs($this->test_user, 'sanctum')->getJson($this->api_endpoint . "?code=xx&name=Test");
        $response->assertStatus(200)
            ->assertJsonFragment(['code' => 'xx', 'name' => 'Test']);

        $language1->forceDelete();
        $language2->forceDelete();
    }

    /**
     * @return void
     */
    public function test_returning_single_language()
    {
        $language = Language::factory()->create(['code' => 'xx', 'name' => 'Test']);

        $response = $this->actingAs($this->test_user, 'sanctum')->getJson($this->api_endpoint . "/{$language->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $language->id,
                    'code' => $language->code,
                    'name' => $language->name
                ],
                'message' => 'Language successfully fetched'
            ]);

        $language->forceDelete();
    }

    /**
     * @return void
     */
    public function test_returning_404_if_language_not_found()
    {
        $response = $this->actingAs($this->test_user, 'sanctum')->getJson($this->api_endpoint . '/9999999999');
        $response->assertStatus(404);
    }

    /**
     * @return void
     */
    public function test_can_create_new_language()
    {
        $data = [
            'code' => 'tl',
            'name' => 'Tagalog'
        ];

        $response = $this->actingAs($this->test_user, 'sanctum')->postJson($this->api_endpoint, $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Language created successfully',
                'data' => $data
            ]);

        $this->assertDatabaseHas('languages', $data);
    }

    /**
     * @return void
     */
    public function test_return_error_creating_missing_field_language()
    {
        $response = $this->actingAs($this->test_user, 'sanctum')->postJson($this->api_endpoint, ['code' => 'xx']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * @return void
     */
    public function test_update_existing_language()
    {
        $language = Language::factory()->create();
        $updated_data = [
            'code' => 'tl',
            'name' => 'Tagalog',
        ];

        $response = $this->actingAs($this->test_user, 'sanctum')->putJson($this->api_endpoint . "/{$language->id}", $updated_data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Language updated successfully',
                'data' => [
                    'id' => $language->id,
                    'code' => 'tl',
                    'name' => 'Tagalog'
                ]
            ]);

        $this->assertDatabaseHas('languages', ['id' => $language->id, 'code' => 'tl', 'name' => 'Tagalog']);
    }

    /**
     * @return void
     */
    public function test_returns_validation_error_updating_existing_code()
    {
        Language::factory()->create(['code' => 'tl']);
        $update_language = Language::factory()->create(['code' => 'jp']);

        $response = $this->actingAs($this->test_user, 'sanctum')->putJson($this->api_endpoint . "/{$update_language->id}", ['code' => 'tl', 'name' => 'Tagalog']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    /**
     * @return void
     */
    public function test_delete_language()
    {
        $language = Language::factory()->create();

        $response = $this->actingAs($this->test_user, 'sanctum')->deleteJson($this->api_endpoint . "/{$language->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('languages', ['id' => $language->id]);
    }

    /**
     * @return void
     */
    public function test_returns_404_missing_delete_language()
    {
        $response = $this->actingAs($this->test_user, 'sanctum')->deleteJson($this->api_endpoint . '/999999999');
        $response->assertStatus(404);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        $this->test_user->forceDelete();

        parent::tearDown();
    }
}
