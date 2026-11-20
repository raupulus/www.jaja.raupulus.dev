<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyRecaptcha;
use App\Models\Category;
use App\Models\Collaborator;
use App\Models\CollaboratorProject;
use App\Models\Content;
use App\Models\ContentOption;
use App\Models\Group;
use App\Models\Page;
use App\Models\Report;
use App\Models\Suggestion;
use App\Models\SuggestionOption;
use App\Models\Type;
use App\Models\User;
use App\Services\CacheInvalidationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DatabasePerformanceAndAtomicityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $user;

    private Type $chistesType;

    private Type $quizType;

    private Group $chistesGroup;

    private Group $quizGroup;

    private Category $generalCategory;

    private Category $techCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Cache::flush();

        $this->generalCategory = $this->ensureGeneralCategory();

        $this->techCategory = Category::create([
            'title' => 'Tecnología',
            'slug' => 'tecnologia',
            'description' => 'Categoría de tecnología',
        ]);

        $this->admin = User::factory()->create([
            'role_id' => 1,
            'nick' => 'admin_perf',
        ]);

        $this->user = User::factory()->create([
            'role_id' => 2,
            'nick' => 'author_perf',
        ]);

        $this->chistesType = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes divertidos',
        ]);

        $this->quizType = Type::create([
            'name' => 'Quiz',
            'slug' => 'quiz',
            'description' => 'Preguntas tipo quiz',
        ]);

        $this->chistesGroup = Group::create([
            'type_id' => $this->chistesType->id,
            'title' => 'Chistes Variados',
            'slug' => 'chistes-variados',
            'description' => 'Grupo general de chistes',
        ]);

        $this->quizGroup = Group::create([
            'type_id' => $this->quizType->id,
            'title' => 'Quiz Tech',
            'slug' => 'quiz-tech',
            'description' => 'Preguntas de tecnología',
        ]);
    }

    public function test_prevent_lazy_loading_is_enabled_outside_production(): void
    {
        $this->assertTrue(Model::preventsLazyLoading());
    }

    public function test_public_web_routes_trigger_zero_lazy_loading_violations_with_quiz_and_relations(): void
    {
        $joke = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->chistesGroup->id,
            'title' => 'Chiste con autor relacional',
            'content' => 'Contenido de prueba para validar eager loading.',
            'uploaded_by' => null,
            'is_adult' => false,
        ]);
        $joke->categories()->sync([$this->generalCategory->id, $this->techCategory->id]);

        $quiz = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->quizGroup->id,
            'title' => 'Quiz de Laravel',
            'content' => '¿Qué helper evita N+1?',
            'uploaded_by' => null,
            'is_adult' => false,
        ]);
        $quiz->categories()->sync([$this->generalCategory->id, $this->techCategory->id]);

        ContentOption::create([
            'content_id' => $quiz->id,
            'value' => 'with()',
            'is_correct' => true,
            'order' => 1,
        ]);
        ContentOption::create([
            'content_id' => $quiz->id,
            'value' => 'all()',
            'is_correct' => false,
            'order' => 2,
        ]);

        Page::create([
            'title' => 'Agradecimientos',
            'slug' => 'agradecimientos',
            'excerpt' => 'Página de agradecimientos',
            'content' => 'Contenido de agradecimientos.',
            'status' => 'published',
        ]);

        $collaborator = Collaborator::create([
            'user_id' => $this->user->id,
            'name' => 'Margaret Hamilton',
            'nick' => 'mhamilton',
            'description' => 'Ingeniera de software',
        ]);

        $project = CollaboratorProject::create([
            'collaborator_id' => $collaborator->id,
            'title' => 'Apollo Bot',
            'slug' => 'apollo-bot',
            'excerpt' => 'Bot comunitario',
            'content' => '## Detalles',
            'type' => 'bot',
            'repository_type' => 'github',
            'status' => 'published',
        ]);

        $routes = [
            route('index'),
            route('content.types.index'),
            route('content.groups.index'),
            route('content.type.group.index', $this->quizType->slug),
            route('content.group.content.random', $this->quizGroup->slug),
            route('content.categories.index'),
            route('content.categoria.content.random', $this->techCategory->slug),
            route('page.index'),
            route('page.show', 'agradecimientos'),
            route('collaborator.index'),
            route('collaborator.show', $collaborator->nick),
            route('collaborator.project.show', [$collaborator->nick, $project->slug]),
        ];

        foreach ($routes as $uri) {
            $obLevel = ob_get_level();
            $this->get($uri)->assertStatus(200);
            $this->assertSame($obLevel, ob_get_level(), "Output buffer leaked on {$uri}");
        }

        // Segunda pasada usando la caché poblada en /tipos y /grupos
        $this->get(route('content.types.index'))->assertStatus(200);
        $this->get(route('content.groups.index'))->assertStatus(200);
    }

    public function test_api_v1_endpoints_trigger_zero_lazy_loading_violations(): void
    {
        $quiz = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->quizGroup->id,
            'title' => 'Pregunta API',
            'content' => '¿Cuánto es 2+2?',
            'uploaded_by' => null,
            'is_adult' => false,
        ]);
        $quiz->categories()->sync([$this->techCategory->id]);
        ContentOption::create([
            'content_id' => $quiz->id,
            'value' => '4',
            'is_correct' => true,
            'order' => 1,
        ]);

        $joke = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->chistesGroup->id,
            'title' => 'Chiste API',
            'content' => 'Chiste de prueba API.',
            'uploaded_by' => null,
            'is_adult' => false,
        ]);
        $joke->categories()->sync([$this->techCategory->id]);

        $this->getJson('/api/v1/random?limit=5')->assertStatus(200);
        $this->getJson('/api/v1/random/'.$this->quizType->slug)->assertStatus(200);

        Sanctum::actingAs($this->user);

        $this->getJson('/api/v1/types')->assertStatus(200);
        $this->getJson('/api/v1/groups')->assertStatus(200);
        $this->getJson('/api/v1/categories')->assertStatus(200);
        $this->getJson('/api/v1/contents/list')->assertStatus(200);
        $this->getJson('/api/v1/type/'.$this->quizType->slug.'/content/random')->assertStatus(200);
        $this->getJson('/api/v1/type/'.$this->quizType->slug.'/category/'.$this->techCategory->slug.'/content/random')->assertStatus(200);
        $this->getJson('/api/v1/group/'.$this->quizGroup->slug.'/content/random')->assertStatus(200);
        $this->getJson('/api/v1/user/'.$this->user->nick.'/content/random')->assertStatus(200);

        $this->postJson('/api/v1/suggestion/send', [
            'nick' => 'tester_api',
            'title' => 'Sugerencia API',
            'content' => 'Contenido enviado por API sin N+1.',
        ])->assertStatus(201);

        $this->postJson('/api/v1/report/send', [
            'content_id' => $joke->id,
            'title' => 'Reporte sin reporter_name',
            'description' => 'Para comprobar que reporter_name no dispara lazy loading.',
        ])->assertStatus(201);
    }

    public function test_admin_report_resource_renders_without_lazy_loading_when_reporter_name_is_null(): void
    {
        $content = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->chistesGroup->id,
            'title' => 'Contenido reportado',
            'content' => 'Texto reportado.',
            'is_adult' => false,
        ]);

        Report::create([
            'user_id' => $this->user->id,
            'reporter_name' => null,
            'reporter_email' => null,
            'reporter_ip' => '127.0.0.1',
            'reportable_type' => Content::class,
            'reportable_id' => $content->id,
            'type' => 'other',
            'title' => 'Reporte sin nombre explícito',
            'description' => 'Comprueba eager loading en Filament.',
            'status' => 'pending',
            'priority' => 'medium',
            'assigned_to' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)
            ->get('/admin/reports')
            ->assertStatus(200);
    }

    public function test_send_suggestion_rolls_back_database_and_image_when_option_creation_fails(): void
    {
        Storage::fake('public');
        $this->withoutMiddleware(VerifyRecaptcha::class);

        $image = UploadedFile::fake()->image('quiz-test.png', 200, 200);
        $shouldFail = true;

        SuggestionOption::creating(function () use (&$shouldFail): void {
            if ($shouldFail) {
                throw new \RuntimeException('Simulated failure creating suggestion option');
            }
        });

        try {
            $response = $this->post(route('suggestion.send'), [
                'type_id' => $this->quizType->id,
                'group_id' => $this->quizGroup->id,
                'nick' => 'acid_tester',
                'title' => 'Quiz atómico',
                'content' => '¿Falla de forma atómica?',
                'answer1' => 'Sí',
                'answer1_correct' => '1',
                'answer2' => 'No',
                'image' => $image,
                'terms' => '1',
            ]);

            $this->assertSame(500, $response->getStatusCode());
        } finally {
            $shouldFail = false;
        }

        $this->assertDatabaseCount('suggestions', 0);
        $this->assertDatabaseCount('suggestion_options', 0);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_suggestion_approve_is_atomic_on_failure_and_preserves_original_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('suggestion-images/original.webp', 'fake-webp-bytes');

        $suggestion = Suggestion::create([
            'type_id' => $this->quizType->id,
            'group_id' => $this->quizGroup->id,
            'nick' => $this->user->nick,
            'title' => 'Quiz para aprobar',
            'content' => '¿Es atómica la aprobación?',
            'image_path' => 'suggestion-images/original.webp',
        ]);

        SuggestionOption::create([
            'suggestion_id' => $suggestion->id,
            'value' => 'Opción 1',
            'is_correct' => true,
            'order' => 1,
        ]);

        $shouldFail = true;

        ContentOption::creating(function () use (&$shouldFail): void {
            if ($shouldFail) {
                throw new \RuntimeException('Simulated failure creating content option');
            }
        });

        $thrown = false;

        try {
            $suggestion->approve();
        } catch (\RuntimeException $e) {
            $thrown = true;
            $this->assertSame('Simulated failure creating content option', $e->getMessage());
        } finally {
            $shouldFail = false;
        }

        $this->assertTrue($thrown);

        $suggestion->refresh();

        $this->assertDatabaseCount('contents', 0);
        $this->assertNull($suggestion->approved_at);
        $this->assertNull($suggestion->deleted_at);
        Storage::disk('public')->assertExists('suggestion-images/original.webp');
        $this->assertEmpty(Storage::disk('public')->files('content-images'));
    }

    public function test_suggestion_approve_succeeds_without_duplicate_categories_and_cleans_source_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('suggestion-images/source.webp', 'fake-webp-bytes');

        $suggestion = Suggestion::create([
            'type_id' => $this->quizType->id,
            'group_id' => $this->quizGroup->id,
            'nick' => $this->user->nick,
            'title' => 'Quiz aprobado',
            'content' => 'Contenido de quiz aprobado.',
            'image_path' => 'suggestion-images/source.webp',
        ]);

        SuggestionOption::create([
            'suggestion_id' => $suggestion->id,
            'value' => 'Respuesta A',
            'is_correct' => true,
            'order' => 1,
        ]);

        $this->actingAs($this->admin);
        $this->assertTrue($suggestion->approve());

        $suggestion->refresh();
        $content = Content::firstOrFail();

        $this->assertNotNull($suggestion->approved_at);
        $this->assertNotNull($suggestion->deleted_at);
        $this->assertSame($this->user->id, $content->user_id);
        $this->assertCount(1, $content->categories()->get());
        $this->assertCount(1, $content->options()->get());
        $this->assertStringStartsWith('content-images/', $content->image);
        Storage::disk('public')->assertExists($content->image);
        Storage::disk('public')->assertMissing('suggestion-images/source.webp');
    }

    public function test_model_changes_invalidate_public_and_panel_caches(): void
    {
        Cache::put('home_contents', 'cached', 60);
        Cache::put('types_index', 'cached', 60);
        Cache::put('groups_index', 'cached', 60);
        Cache::put('groups_index_by_type_'.$this->chistesType->id, 'cached', 60);
        Cache::put('categories_index', 'cached', 60);
        Cache::put(CacheInvalidationService::ADMIN_CONTENT_OVERVIEW_KEY, ['cached'], 60);

        $content = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->chistesGroup->id,
            'title' => 'Contenido caché',
            'content' => 'Texto para invalidar caché.',
            'is_adult' => false,
        ]);

        $this->assertFalse(Cache::has('home_contents'));
        $this->assertFalse(Cache::has('types_index'));
        $this->assertFalse(Cache::has('groups_index'));
        $this->assertFalse(Cache::has('groups_index_by_type_'.$this->chistesType->id));
        $this->assertFalse(Cache::has('categories_index'));
        $this->assertFalse(Cache::has(CacheInvalidationService::ADMIN_CONTENT_OVERVIEW_KEY));

        Cache::put('pages_index', 'cached', 60);
        $page = Page::create([
            'title' => 'Página caché',
            'slug' => 'pagina-cache',
            'excerpt' => 'Resumen',
            'content' => 'Contenido',
            'status' => 'published',
        ]);
        $this->assertFalse(Cache::has('pages_index'));

        Cache::put('page_collaborators_index', 'cached', 60);
        $collaborator = Collaborator::create([
            'user_id' => $this->user->id,
            'name' => 'Colab Cache',
            'nick' => 'colab_cache',
            'description' => 'Desc',
        ]);
        $this->assertFalse(Cache::has('page_collaborators_index'));

        $projectKey = CacheInvalidationService::collaboratorShowProjectKey($collaborator->id, 999);
        Cache::put($projectKey, 'cached', 60);
        Cache::put('page_collaborator_'.$collaborator->id.'_projects', 'cached', 60);

        $project = CollaboratorProject::create([
            'collaborator_id' => $collaborator->id,
            'title' => 'Proyecto Cache',
            'slug' => 'proyecto-cache',
            'excerpt' => 'Resumen',
            'content' => 'Contenido',
            'type' => 'web',
            'repository_type' => 'github',
            'status' => 'published',
        ]);
        $this->assertFalse(Cache::has('page_collaborator_'.$collaborator->id.'_projects'));

        Cache::put(CacheInvalidationService::collaboratorShowProjectKey($collaborator->id, $project->id), 'cached', 60);
        $project->update(['excerpt' => 'Resumen actualizado']);
        $this->assertFalse(Cache::has(CacheInvalidationService::collaboratorShowProjectKey($collaborator->id, $project->id)));

        // Evita advertencias por variable sin usar en static analysis posterior
        $this->assertNotNull($content->id);
        $this->assertNotNull($page->id);
    }
}
