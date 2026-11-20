<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Content;
use App\Models\Group;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAndValidationStabilityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $user;

    private Type $type;

    private Group $group;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        Category::create([
            'id' => 1,
            'title' => 'General',
            'slug' => 'general',
            'description' => 'Categoría general',
        ]);

        $this->admin = User::factory()->create([
            'role_id' => 1,
            'nick' => 'admin_api',
        ]);

        $this->user = User::factory()->create([
            'role_id' => 2,
            'nick' => 'user_api',
        ]);

        $this->type = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes variados',
        ]);

        $this->group = Group::create([
            'type_id' => $this->type->id,
            'title' => 'Chistes Cortos',
            'slug' => 'chistes-cortos',
            'description' => 'Chistes muy cortos',
        ]);
    }

    public function test_api_send_report_without_description_returns_201_and_persists(): void
    {
        $content = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'title' => 'Chiste reportado',
            'content' => 'Texto del chiste',
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/report/send', [
            'content_id' => $content->id,
            'title' => 'Contenido repetido',
            'type' => 'spam',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Contenido repetido',
                    'type' => 'spam',
                    'content_id' => $content->id,
                ],
            ]);

        $this->assertDatabaseHas('reports', [
            'reportable_id' => $content->id,
            'title' => 'Contenido repetido',
            'description' => '',
            'assigned_to' => $this->admin->id,
        ]);
    }

    public function test_api_send_suggestion_without_nick_or_title_uses_defaults(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/suggestion/send', [
            'content' => '¿Qué le dice un bit a otro bit? Nos vemos en el bus.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Sugerencia',
                    'nick' => null,
                    'type' => 'Chistes',
                ],
            ]);

        $this->assertDatabaseHas('suggestions', [
            'title' => 'Sugerencia',
            'nick' => null,
            'type_id' => $this->type->id,
        ]);
    }

    public function test_api_send_suggestion_returns_422_instead_of_500_when_default_type_is_missing(): void
    {
        $this->type->delete();

        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/suggestion/send', [
            'content' => 'Sugerencia sin tipo en base de datos.',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('type_id');
    }

    public function test_api_groups_pagination_uses_standard_page_parameter(): void
    {
        Group::create([
            'type_id' => $this->type->id,
            'title' => 'Chistes Variados',
            'slug' => 'chistes-variados',
            'description' => 'Más chistes',
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/groups?page=1&limit=1');

        $response->assertStatus(200)
            ->assertJsonPath('meta.pagination.current_page', 1);

        $nextPageUrl = $response->json('meta.pagination.next_page_url');
        $this->assertNotNull($nextPageUrl);
        $this->assertStringContainsString('page=2', $nextPageUrl);
        $this->assertStringNotContainsString('groups_index=', $nextPageUrl);
    }

    public function test_api_rejects_non_integer_pagination_or_excessive_exclude_groups_with_422(): void
    {
        Sanctum::actingAs($this->user);

        $this->getJson('/api/v1/groups?limit=abc')
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->getJson('/api/v1/contents/list?after_id=abc')
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $tooManyGroups = range(1, 51);

        $this->getJson('/api/v1/contents/list?'.http_build_query(['exclude_groups' => $tooManyGroups]))
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_web_suggestion_requires_accepted_terms(): void
    {
        config(['google.recaptcha.secret_key' => null]);

        $this->from('/')
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.50'])
            ->post(route('suggestion.send'), [
                'type_id' => $this->type->id,
                'title' => 'Chiste sin términos',
                'content' => 'Contenido de prueba',
            ])
            ->assertSessionHasErrors('terms');

        $this->from('/')
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.51'])
            ->post(route('suggestion.send'), [
                'type_id' => $this->type->id,
                'title' => 'Chiste con términos',
                'content' => 'Contenido de prueba aceptado',
                'terms' => '1',
            ])
            ->assertRedirect(route('index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('suggestions', [
            'title' => 'Chiste con términos',
            'content' => 'Contenido de prueba aceptado',
        ]);
    }
}
