<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TranslationControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @var string
     */
    private string $api_endpoint = "/api/translations";

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
    public function test_returning_paginated_translations(): void
    {
        $translation = Translation::factory()->create();

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

        $translation->forceDelete();
    }

    /**
     * @return void
     */
    public function test_filter_translations_by_tag_ids_keys_and_value()
    {
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $language = Language::factory()->create();

        $translation1 = Translation::factory()->create(['key' => 'hello', 'value' => 'Hello', 'language_id' => $language->id]);
        $translation1->tags()->attach($tag1);

        $translation2 = Translation::factory()->create(['key' => 'goodbye', 'value' => 'Goodbye', 'language_id' => $language->id]);
        $translation2->tags()->attach($tag2);

        $response = $this->actingAs($this->test_user, 'sanctum')->getJson($this->api_endpoint . "?tag_ids={$tag1->id}&keys=hello&value=Hello");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['key' => 'hello']);

        $translation1->forceDelete();
        $translation2->forceDelete();
        $tag1->forceDelete();
        $tag2->forceDelete();
        $language->forceDelete();
    }

    /**
     * @return void
     */
    public function test_get_single_translation()
    {
        $translation = Translation::factory()->create();

        $response = $this->actingAs($this->test_user, 'sanctum')->getJson($this->api_endpoint . "/{$translation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $translation->id,
                    'key' => $translation->key,
                    'value' => $translation->value,
                    'language_id' => $translation->language_id,
                ],
                'message' => 'Translation fetched successfully'
            ]);

        $translation->forceDelete();
    }

    /**
     * @return void
     */
    public function test_return_404_translation_not_found()
    {
        $response = $this->actingAs($this->test_user, 'sanctum')->getJson($this->api_endpoint . "/999999999");
        $response->assertStatus(404);
    }

    /**
     * @return void
     */
    public function test_create_a_new_translation()
    {
        $language = Language::factory()->create();
        $translationData = [
            'key' => 'test_key',
            'value' => 'test_value',
            'language_id' => $language->id,
        ];

        $response = $this->actingAs($this->test_user, 'sanctum')->postJson($this->api_endpoint, $translationData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Translation created successfully',
                'data' => $translationData
            ]);

        $this->assertDatabaseHas('translations', $translationData);

        Translation::where('key', $translationData['key'])->delete();
        $language->forceDelete();
    }

    /**
     * @return void
     */
    public function test_returns_validation_error_creating_translation_missing_field()
    {
        $response = $this->actingAs($this->test_user, 'sanctum')->postJson($this->api_endpoint, ['key' => 'test_key']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['value', 'language_id']);
    }

    /**
     * @return void
     */
    public function test_update_an_existing_translation()
    {
        $translation = Translation::factory()->create();
        $language = Language::factory()->create();
        $updated_data = [
            'key' => 'updated_key',
            'value' => 'updated_value',
            'language_id' => $language->id,
        ];

        $response = $this->actingAs($this->test_user, 'sanctum')->putJson($this->api_endpoint . "/{$translation->id}", $updated_data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Translation updated successfully',
                'data' => array_merge(['id' => $translation->id], $updated_data)
            ]);

        $this->assertDatabaseHas('translations', ['id' => $translation->id, 'key' => 'updated_key', 'value' => 'updated_value']);

        Translation::where('key', $updated_data['key'])->delete();
        $language->forceDelete();
    }

    /**
     * @return void
     */
    public function test_delete_translation()
    {
        $translation = Translation::factory()->create();

        $response = $this->actingAs($this->test_user, 'sanctum')->deleteJson($this->api_endpoint . "/{$translation->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);

        $translation->forceDelete();
    }

    /**
     * @return void
     */
    public function test_return_404_delete_missing_translaction()
    {
        $response = $this->actingAs($this->test_user, 'sanctum')->deleteJson($this->api_endpoint . "/99999999");
        $response->assertStatus(404);
    }

    /**
     * @return void
     */
    public function test_export_translation_per_locale()
    {
        $language = Language::factory()->create(['code' => 'tl']);
        $translation1 = Translation::factory()->create(['key' => 'greeting', 'value' => 'Magandang Araw', 'language_id' => $language->id]);
        $translation2 = Translation::factory()->create(['key' => 'goodbye', 'value' => 'Paalam', 'language_id' => $language->id]);

        $response = $this->actingAs($this->test_user, 'sanctum')->getJson($this->api_endpoint . "/export/tl");

        $response->assertStatus(200)
            ->assertJson([
                'greeting' => 'Magandang Araw',
                'goodbye' => 'Paalam'
            ]);

        $translation1->forceDelete();
        $translation2->forceDelete();
        $language->forceDelete();
    }

    /**
     * @return void
     */
    public function test_return_404_missing_locale_translation()
    {
        $response = $this->actingAs($this->test_user, 'sanctum')->getJson($this->api_endpoint . "/export/xxx");
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Language not found',
                'success' => false
            ]);
    }

    /**
     * @return void
     */
    public function test_get_endpoint_is_performant()
    {
        $languages = Language::factory()->count(5)->create();

        $translations = [];
        foreach ($languages as $language) {
            $translations[] = Translation::factory()->count(100)->create(['language_id' => $language->id]);
        }

        $num_req = 50;
        $max_execution_time = 1000;

        $start_time = microtime(true);

        for ($i = 0; $i < $num_req; $i++) {
            $response = $this->actingAs($this->test_user, 'sanctum')->getJson($this->api_endpoint);
            $response->assertStatus(200);
        }

        $end_time = microtime(true);
        $total_time = ($end_time - $start_time) * 1000;

        $this->assertLessThan($max_execution_time, $total_time, "The get endpoint is too slow, it took {$total_time}ms for {$num_req} requests");
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
