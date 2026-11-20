<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collaborator;
use App\Models\CollaboratorProject;
use App\Models\Content;
use App\Models\Group;
use App\Models\Page;
use App\Models\Report;
use App\Models\Suggestion;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AccessControlAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    private User $otherUser;

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
            'nick' => 'admin_user',
        ]);

        $this->regularUser = User::factory()->create([
            'role_id' => 2,
            'nick' => 'normal_user',
        ]);

        $this->otherUser = User::factory()->create([
            'role_id' => 2,
            'nick' => 'other_user',
        ]);
    }

    public function test_regular_user_cannot_access_admin_panel_or_admin_actions_but_can_access_panel(): void
    {
        $this->actingAs($this->regularUser)
            ->get('/admin')
            ->assertStatus(403);

        $this->actingAs($this->regularUser)
            ->get('/admin/action/generate/sitemap')
            ->assertStatus(405);

        $this->actingAs($this->regularUser)
            ->post('/admin/action/generate/sitemap')
            ->assertStatus(403);

        $this->actingAs($this->regularUser)
            ->get('/panel')
            ->assertStatus(200);
    }

    public function test_admin_can_access_both_admin_and_panel(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/panel')
            ->assertStatus(200);
    }

    public function test_navbar_routes_dashboard_link_according_to_role(): void
    {
        $userResponse = $this->actingAs($this->regularUser)->get('/');
        $userResponse->assertStatus(200);
        $userResponse->assertSee(route('filament.panel.pages.dashboard'), false);
        $userResponse->assertDontSee(route('filament.admin.pages.dashboard'), false);

        $adminResponse = $this->actingAs($this->admin)->get('/');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee(route('filament.admin.pages.dashboard'), false);
    }

    public function test_policies_isolate_regular_user_resources_and_admin_models(): void
    {
        $type = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes',
        ]);

        $group = Group::create([
            'type_id' => $type->id,
            'title' => 'Grupo Test',
            'slug' => 'grupo-test',
            'description' => 'Desc',
        ]);

        $ownContent = Content::create([
            'user_id' => $this->regularUser->id,
            'group_id' => $group->id,
            'title' => 'Propio',
            'content' => 'Contenido propio',
        ]);

        $otherContent = Content::create([
            'user_id' => $this->otherUser->id,
            'group_id' => $group->id,
            'title' => 'Ajeno',
            'content' => 'Contenido ajeno',
        ]);

        $ownCollaborator = Collaborator::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Colab Uno',
            'nick' => 'colab_uno',
            'description' => 'Desc colab',
        ]);

        $otherCollaborator = Collaborator::create([
            'user_id' => $this->otherUser->id,
            'name' => 'Colab Dos',
            'nick' => 'colab_dos',
            'description' => 'Desc colab',
        ]);

        $page = Page::create([
            'title' => 'Normas',
            'slug' => 'normas',
            'excerpt' => 'Normas de la comunidad',
            'content' => 'Contenido',
        ]);

        // Usuario normal: solo lo suyo en Content y Collaborator; bloqueado en recursos admin.
        $this->assertTrue($this->regularUser->can('view', $ownContent));
        $this->assertTrue($this->regularUser->can('update', $ownContent));
        $this->assertFalse($this->regularUser->can('view', $otherContent));
        $this->assertFalse($this->regularUser->can('update', $otherContent));
        $this->assertFalse($this->regularUser->can('delete', $otherContent));

        $this->assertTrue($this->regularUser->can('view', $ownCollaborator));
        $this->assertTrue($this->regularUser->can('update', $ownCollaborator));
        $this->assertFalse($this->regularUser->can('view', $otherCollaborator));
        $this->assertFalse($this->regularUser->can('update', $otherCollaborator));

        $this->assertFalse($this->regularUser->can('viewAny', User::class));
        $this->assertFalse($this->regularUser->can('viewAny', Page::class));
        $this->assertFalse($this->regularUser->can('update', $page));
        $this->assertFalse($this->regularUser->can('viewAny', Category::class));
        $this->assertFalse($this->regularUser->can('viewAny', Group::class));
        $this->assertFalse($this->regularUser->can('viewAny', Type::class));
        $this->assertFalse($this->regularUser->can('viewAny', Report::class));
        $this->assertFalse($this->regularUser->can('viewAny', Suggestion::class));

        // Admin: acceso completo.
        $this->assertTrue($this->admin->can('update', $otherContent));
        $this->assertTrue($this->admin->can('update', $page));
        $this->assertTrue($this->admin->can('viewAny', User::class));
    }

    public function test_collaborator_project_show_enforces_ownership_and_published_status(): void
    {
        $collabOne = Collaborator::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Colaborador Uno',
            'nick' => 'colaborador_uno',
            'description' => 'Desc',
        ]);

        $collabTwo = Collaborator::create([
            'user_id' => $this->otherUser->id,
            'name' => 'Colaborador Dos',
            'nick' => 'colaborador_dos',
            'description' => 'Desc',
        ]);

        $draftProject = CollaboratorProject::create([
            'collaborator_id' => $collabOne->id,
            'title' => 'Proyecto Borrador',
            'slug' => 'proyecto-borrador',
            'excerpt' => 'Resumen',
            'content' => 'Contenido',
            'type' => 'web',
            'repository_type' => 'github',
            'status' => 'draft',
        ]);

        $otherPublishedProject = CollaboratorProject::create([
            'collaborator_id' => $collabTwo->id,
            'title' => 'Proyecto Dos',
            'slug' => 'proyecto-dos',
            'excerpt' => 'Resumen dos',
            'content' => 'Contenido dos',
            'type' => 'web',
            'repository_type' => 'github',
            'status' => 'published',
        ]);

        $ownPublishedProject = CollaboratorProject::create([
            'collaborator_id' => $collabOne->id,
            'title' => 'Proyecto Publicado',
            'slug' => 'proyecto-publicado',
            'excerpt' => 'Resumen uno',
            'content' => 'Contenido uno',
            'type' => 'web',
            'repository_type' => 'github',
            'status' => 'published',
        ]);

        // Proyecto en borrador: 404
        $this->get(route('collaborator.project.show', [$collabOne->nick, $draftProject->slug]))
            ->assertStatus(404);

        // Proyecto publicado perteneciente a otro colaborador (IDOR): 404
        $this->get(route('collaborator.project.show', [$collabOne->nick, $otherPublishedProject->slug]))
            ->assertStatus(404);

        // Proyecto publicado del colaborador correcto: 200
        $this->get(route('collaborator.project.show', [$collabOne->nick, $ownPublishedProject->slug]))
            ->assertStatus(200)
            ->assertSee('Proyecto Publicado');
    }

    public function test_group_and_category_descriptions_are_escaped_against_xss(): void
    {
        $payload = '<script>alert("xss")</script>';

        $type = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes',
        ]);

        $group = Group::create([
            'type_id' => $type->id,
            'title' => 'Grupo XSS',
            'slug' => 'grupo-xss',
            'description' => $payload,
        ]);

        $category = Category::create([
            'title' => 'Categoría XSS',
            'slug' => 'categoria-xss',
            'description' => $payload,
        ]);

        $content = Content::create([
            'user_id' => $this->regularUser->id,
            'group_id' => $group->id,
            'title' => 'Contenido Seguro',
            'content' => 'Texto limpio',
            'is_adult' => false,
        ]);

        $category->contents()->sync([$content->id]);

        $groupResponse = $this->get(route('content.group.content.random', $group->slug));
        $groupResponse->assertStatus(200);
        $groupResponse->assertDontSee($payload, false);
        $groupResponse->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);

        $categoryResponse = $this->get(route('content.categoria.content.random', $category->slug));
        $categoryResponse->assertStatus(200);
        $categoryResponse->assertDontSee($payload, false);
        $categoryResponse->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
    }

    public function test_verify_recaptcha_validates_action_score_and_network_errors(): void
    {
        config(['google.recaptcha.secret_key' => 'test-secret']);

        $type = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes',
        ]);

        $payload = [
            'type_id' => $type->id,
            'title' => 'Título de prueba',
            'content' => 'Contenido de prueba',
            'terms' => '1',
            'g_recaptcha' => 'fake-token',
        ];

        // Acción incorrecta -> rechaza
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'other_action',
            ], 200),
        ]);

        $this->from('/')
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->post(route('suggestion.send'), $payload)
            ->assertSessionHasErrors('g_recaptcha');

        // Puntuación baja -> rechaza
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.3,
                'action' => 'suggestion_send',
            ], 200),
        ]);

        $this->from('/')
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.11'])
            ->post(route('suggestion.send'), $payload)
            ->assertSessionHasErrors('g_recaptcha');

        // Error de conexión -> error de validación controlado (sin 500)
        Http::swap(new Factory);
        Http::fake(function () {
            throw new ConnectionException('Timeout');
        });

        $this->from('/')
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.12'])
            ->post(route('suggestion.send'), $payload)
            ->assertSessionHasErrors('g_recaptcha');

        // Respuesta válida -> acepta y redirige con éxito
        Http::swap(new Factory);
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'suggestion_send',
            ], 200),
        ]);

        $this->from('/')
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.13'])
            ->post(route('suggestion.send'), $payload)
            ->assertRedirect(route('index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');
    }
}
