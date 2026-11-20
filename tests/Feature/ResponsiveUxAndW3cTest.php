<?php

namespace Tests\Feature;

use App\Models\Collaborator;
use App\Models\CollaboratorProject;
use App\Models\Group;
use App\Models\Page;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ResponsiveUxAndW3cTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Cache::flush();

        $this->ensureGeneralCategory();
    }

    public function test_custom_404_page_renders_integrated_layout_with_noindex(): void
    {
        $response = $this->get('/ruta-que-no-existe-en-jaja');

        $response->assertStatus(404);
        $response->assertSee('404 · Página no encontrada');
        $response->assertSee('noindex, nofollow');
        $response->assertSee(route('index'), false);
    }

    public function test_public_listings_render_empty_states_gracefully(): void
    {
        $this->get(route('content.types.index'))
            ->assertStatus(200)
            ->assertSee('Todavía no hay tipos de contenido disponibles');

        $this->get(route('content.groups.index'))
            ->assertStatus(200)
            ->assertSee('No hay grupos de contenido disponibles todavía');

        $this->get(route('content.categories.index'))
            ->assertStatus(200)
            ->assertSee('No hay categorías con contenido disponible todavía');

        $this->get(route('page.index'))
            ->assertStatus(200)
            ->assertSee('Todavía no hay páginas publicadas');

        $this->get(route('collaborator.index'))
            ->assertStatus(200)
            ->assertSee('Todavía no hay colaboradores publicados');
    }

    public function test_suggestion_form_preserves_old_group_id_quiz_correct_and_terms_after_validation_error(): void
    {
        $quizType = Type::create([
            'name' => 'Quiz',
            'slug' => 'quiz',
            'description' => 'Preguntas quiz',
        ]);

        $quizGroup = Group::create([
            'type_id' => $quizType->id,
            'title' => 'Quiz de Programación',
            'slug' => 'quiz-programacion',
            'description' => 'Preguntas técnicas',
        ]);

        // Enviar sin aceptar términos para forzar error de validación y comprobar repoblado de old()
        $response = $this->followingRedirects()
            ->from(route('index'))
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.99'])
            ->post(route('suggestion.send'), [
                'type_id' => $quizType->id,
                'group_id' => $quizGroup->id,
                'title' => 'Pregunta PHP',
                'content' => '¿Qué versión introdujo fibras?',
                'answer1' => 'PHP 7.4',
                'answer2' => 'PHP 8.1',
                'answer2_correct' => '1',
            ]);

        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertMatchesRegularExpression(
            '/<option\s+value="'.$quizGroup->id.'"\s+selected/s',
            $html
        );
        $this->assertMatchesRegularExpression(
            '/<input[^>]*name="answer2_correct"[^>]*checked/s',
            $html
        );
        $this->assertStringContainsString('field-error', $html);
    }

    public function test_public_views_meet_w3c_markup_breadcrumbs_and_project_excerpt_fixes(): void
    {
        $user = User::factory()->create(['nick' => 'w3c_dev']);

        $collaborator = Collaborator::create([
            'user_id' => $user->id,
            'name' => 'Grace Hopper',
            'nick' => 'grace_h',
            'description' => 'DESCRIPCION_DEL_COLABORADOR_NO_PROYECTO',
            'website' => 'https://example.org',
        ]);

        $project = CollaboratorProject::create([
            'collaborator_id' => $collaborator->id,
            'title' => 'Compilador Abierto',
            'slug' => 'compilador-abierto',
            'excerpt' => 'RESUMEN_EXCLUSIVO_DEL_PROYECTO',
            'content' => '## Detalles\nDocumentación técnica.',
            'type' => 'web',
            'repository_type' => 'github',
            'status' => 'published',
        ]);

        Page::create([
            'title' => 'Sobre JaJa Project',
            'slug' => 'sobre-jaja',
            'excerpt' => 'Acerca de la comunidad.',
            'content' => 'Contenido de prueba.',
            'status' => 'published',
        ]);

        $homeHtml = $this->get(route('index'))->assertStatus(200)->getContent();
        $this->assertStringContainsString('<header class="site-header">', $homeHtml);
        $this->assertStringContainsString('<nav class="navbar" aria-label="Navegación principal">', $homeHtml);
        $this->assertStringContainsString('type="button"', $homeHtml);
        $this->assertStringContainsString('id="acceptCookies"', $homeHtml);
        $this->assertStringNotContainsString('src=""', $homeHtml);
        $this->assertStringNotContainsString('X-UA-Compatible', $homeHtml);
        $this->assertStringNotContainsString('xml:space="preserve"', $homeHtml);
        $this->assertDoesNotMatchRegularExpression('/<a[^>]*btn-dc-bot[^>]*\balt="/s', $homeHtml);

        $collabsHtml = $this->get(route('collaborator.index'))->assertStatus(200)->getContent();
        $this->assertStringContainsString('<nav class="breadcrumbs"', $collabsHtml);
        $this->assertStringContainsString('🌐 Sitio web', $collabsHtml);
        $this->assertStringContainsString('<article class="desarrollador-card">', $collabsHtml);

        $projectHtml = $this->get(route('collaborator.project.show', [$collaborator->nick, $project->slug]))
            ->assertStatus(200)
            ->getContent();
        $this->assertStringContainsString('RESUMEN_EXCLUSIVO_DEL_PROYECTO', $projectHtml);
        $this->assertStringNotContainsString('DESCRIPCION_DEL_COLABORADOR_NO_PROYECTO', $projectHtml);
        $this->assertStringNotContainsString('<-', $projectHtml);

        $pagesHtml = $this->get(route('page.index'))->assertStatus(200)->getContent();
        $this->assertStringContainsString('<nav class="breadcrumbs"', $pagesHtml);
        $this->assertStringContainsString('<article class="card card-content', $pagesHtml);
    }
}
