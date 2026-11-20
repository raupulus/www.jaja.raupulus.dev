<?php

namespace Tests\Feature;

use App\Helpers\PublishToSocialHelper;
use App\Models\Category;
use App\Models\Content;
use App\Models\ContentOption;
use App\Models\Group;
use App\Models\Page;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class ApiV1ContractTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $user;

    private Type $chistesType;

    private Type $quizType;

    private Group $chistesGroup;

    private Group $quizGroup;

    private Category $generalCategory;

    private Category $devCategory;

    private Content $jokeContent;

    private Content $quizContent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Cache::flush();

        $this->generalCategory = $this->ensureGeneralCategory();

        $this->devCategory = Category::create([
            'title' => 'Programación',
            'slug' => 'programacion',
            'description' => 'Chistes técnicos',
        ]);

        $this->admin = User::factory()->create([
            'role_id' => 1,
            'nick' => 'admin_contract',
        ]);

        $this->user = User::factory()->create([
            'role_id' => 2,
            'email' => 'botuser@raupulus.dev',
            'password' => bcrypt('SuperSecret123!'),
            'nick' => 'botuser',
        ]);

        $this->chistesType = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes de la comunidad',
        ]);

        $this->quizType = Type::create([
            'name' => 'Quiz',
            'slug' => 'quiz',
            'description' => 'Preguntas tipo quiz',
        ]);

        $this->chistesGroup = Group::create([
            'type_id' => $this->chistesType->id,
            'title' => 'Chistes Devs',
            'slug' => 'chistes-devs',
            'description' => 'Chistes de desarrolladores',
        ]);

        $this->quizGroup = Group::create([
            'type_id' => $this->quizType->id,
            'title' => 'Quiz PHP',
            'slug' => 'quiz-php',
            'description' => 'Preguntas PHP',
        ]);

        $this->jokeContent = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->chistesGroup->id,
            'title' => 'El compilador paciente',
            'content' => 'Un desarrollador entra a un bar y pide 1 cerveza, 0 cervezas, 999999 cervezas.',
            'uploaded_by' => null,
            'is_adult' => false,
        ]);
        $this->jokeContent->categories()->sync([$this->generalCategory->id, $this->devCategory->id]);

        $this->quizContent = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->quizGroup->id,
            'title' => 'Pregunta PSR',
            'content' => '¿Qué PSR define el autoloading moderno en PHP?',
            'uploaded_by' => null,
            'is_adult' => false,
        ]);
        $this->quizContent->categories()->sync([$this->generalCategory->id, $this->devCategory->id]);

        ContentOption::create([
            'content_id' => $this->quizContent->id,
            'value' => 'PSR-4',
            'is_correct' => true,
            'order' => 1,
        ]);
    }

    public function test_all_fifteen_api_v1_endpoints_respect_retrocompatible_json_contracts(): void
    {
        // 1 & 2. Públicos sin autenticación
        $this->getJson('/api/v1/random?limit=2')
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [['id', 'title', 'content', 'urlImage', 'uploader', 'options']],
                'message',
                'meta' => ['total_items', 'limit'],
            ]);

        $this->getJson('/api/v1/random/chistes')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.total_items', 1);

        // 3. Login y token Bearer
        $this->postJson('/api/v1/auth/login', [
            'email' => 'botuser@raupulus.dev',
            'password' => 'WrongPassword123',
            'device_name' => 'DiscordBot',
        ])->assertStatus(401)->assertJsonPath('success', false);

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'botuser@raupulus.dev',
            'password' => 'SuperSecret123!',
            'device_name' => 'DiscordBot',
        ]);
        $loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['token', 'user' => ['name', 'nick', 'urlImage']],
                'message',
            ]);

        $token = $loginResponse->json('data.token');
        $headers = ['Authorization' => 'Bearer '.$token];

        // 4. Auth user
        $this->withHeaders($headers)
            ->getJson('/api/v1/auth/user')
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['name', 'nick', 'urlImage', 'email', 'email_verified_at'],
                'message',
            ])
            ->assertJsonPath('data.nick', 'botuser');

        // 5. Types
        $this->withHeaders($headers)
            ->getJson('/api/v1/types')
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [['name', 'slug', 'description', 'urlImage']],
                'message',
                'meta' => ['total_items'],
            ]);

        // 6. Groups
        $this->withHeaders($headers)
            ->getJson('/api/v1/groups?page=1&limit=10')
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [['title', 'slug', 'urlImage']],
                'message',
                'meta' => ['pagination' => ['current_page', 'per_page', 'total', 'last_page']],
            ]);

        // 7. Categories
        $this->withHeaders($headers)
            ->getJson('/api/v1/categories?page=1&limit=10')
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [['title', 'slug', 'description', 'urlImage']],
                'message',
                'meta' => ['pagination' => ['current_page', 'per_page', 'total', 'last_page']],
            ]);

        // 8. Random por tipo autenticado
        $this->withHeaders($headers)
            ->getJson('/api/v1/type/quiz/content/random')
            ->assertStatus(200)
            ->assertJsonPath('meta.type_slug', 'quiz')
            ->assertJsonStructure(['data' => [['options']]]);

        // 9. Random por tipo y categoría
        $this->withHeaders($headers)
            ->getJson('/api/v1/type/chistes/category/programacion/content/random')
            ->assertStatus(200)
            ->assertJsonPath('meta.category_slug', 'programacion');

        // 10. Random por grupo
        $this->withHeaders($headers)
            ->getJson('/api/v1/group/chistes-devs/content/random')
            ->assertStatus(200)
            ->assertJsonPath('meta.group_slug', 'chistes-devs');

        // 11. Random por usuario
        $this->withHeaders($headers)
            ->getJson('/api/v1/user/botuser/content/random')
            ->assertStatus(200)
            ->assertJsonPath('meta.user', 'botuser');

        // 12. Listado paginado con filtros
        $this->withHeaders($headers)
            ->getJson('/api/v1/contents/list?after_id=0&limit=10')
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [['id', 'content', 'uploaded_by']],
                'message',
                'meta' => ['pagination' => ['current_page', 'per_page', 'total']],
            ]);

        $this->withHeaders($headers)
            ->getJson('/api/v1/contents/list?exclude_groups[]='.$this->chistesGroup->id)
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');

        // 13. Enviar sugerencia
        $this->withHeaders($headers)
            ->postJson('/api/v1/suggestion/send', [
                'nick' => 'botuser',
                'title' => 'Sugerencia contrato',
                'content' => 'Chiste enviado desde test de contrato.',
            ])
            ->assertStatus(201)
            ->assertJsonStructure(['success', 'data' => ['title', 'content', 'type', 'nick'], 'message']);

        // 14. Enviar reporte
        $this->withHeaders($headers)
            ->postJson('/api/v1/report/send', [
                'content_id' => $this->jokeContent->id,
                'title' => 'Reporte contrato',
                'type' => 'spam',
                'description' => 'Descripción válida de reporte.',
            ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'title', 'type', 'status', 'content_id', 'reporter_name', 'created_at'],
                'message',
            ]);

        // 15. Logout revoca el token actual
        $this->withHeaders($headers)
            ->postJson('/api/v1/auth/logout')
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_openapi_spec_documents_all_fifteen_endpoints_and_api_page_anchors_match_scribe_html(): void
    {
        $openApiPath = public_path('docs/openapi.yaml');
        $docsHtmlPath = public_path('docs/index.html');

        $this->assertFileExists($openApiPath);
        $this->assertFileExists($docsHtmlPath);

        $spec = Yaml::parseFile($openApiPath);
        $documentedPaths = array_keys($spec['paths'] ?? []);

        $expectedPaths = [
            '/api/v1/auth/login',
            '/api/v1/auth/logout',
            '/api/v1/auth/user',
            '/api/v1/random',
            '/api/v1/random/{type_slug}',
            '/api/v1/suggestion/send',
            '/api/v1/report/send',
            '/api/v1/types',
            '/api/v1/groups',
            '/api/v1/categories',
            '/api/v1/type/{type_slug}/content/random',
            '/api/v1/type/{type_slug}/category/{categorySlug}/content/random',
            '/api/v1/group/{group_slug}/content/random',
            '/api/v1/user/{nick}/content/random',
            '/api/v1/contents/list',
        ];

        foreach ($expectedPaths as $expectedPath) {
            $this->assertContains($expectedPath, $documentedPaths, "Missing OpenAPI path: {$expectedPath}");
        }

        Page::create([
            'title' => 'API Oficial',
            'slug' => 'api',
            'excerpt' => 'Documentación de la API',
            'content' => 'Guía de integración.',
            'status' => 'published',
        ]);

        $pageResponse = $this->get(route('page.show', 'api'));
        $pageResponse->assertStatus(200);

        $pageHtml = $pageResponse->getContent();
        $docsHtml = file_get_contents($docsHtmlPath);

        preg_match_all('/href="[^"]*\/docs#([^"]+)"/', (string) $pageHtml, $matches);
        $this->assertNotEmpty($matches[1]);

        foreach ($matches[1] as $anchorId) {
            $this->assertStringContainsString(
                'id="'.$anchorId.'"',
                (string) $docsHtml,
                "Broken Scribe docs anchor #{$anchorId} in /pagina/api"
            );
        }
    }

    public function test_publish_to_social_helper_uses_configurable_url_timeouts_and_handles_errors(): void
    {
        config([
            'services.social_publisher.url' => 'https://social.example.test/publish',
            'services.social_publisher.connect_timeout' => 3,
            'services.social_publisher.timeout' => 9,
        ]);

        Http::fake([
            'https://social.example.test/publish' => Http::response(['success' => true], 200),
        ]);

        $this->assertTrue(PublishToSocialHelper::publishContent($this->jokeContent));

        Http::assertSent(function (HttpClientRequest $request): bool {
            $data = $request->data();

            return $request->url() === 'https://social.example.test/publish'
                && ($data['project'] ?? null) === 'jajaproject'
                && in_array('Chiste', $data['hashtags'] ?? [], true)
                && in_array('Programación', $data['hashtags'] ?? [], true)
                && ! in_array('General', $data['hashtags'] ?? [], true);
        });

        $this->jokeContent->refresh();
        $this->assertNotNull($this->jokeContent->last_social_published);

        // Contenido adulto rechazado sin llamada HTTP
        $adultContent = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->chistesGroup->id,
            'title' => 'Adulto',
            'content' => 'Contenido adulto',
            'is_adult' => true,
        ]);

        Http::fake();
        try {
            PublishToSocialHelper::publishContent($adultContent);
            $this->fail('Adult content must be rejected before publishing.');
        } catch (\Exception $e) {
            $this->assertStringContainsString('no es apto', $e->getMessage());
        }
        Http::assertNothingSent();
    }

    public function test_publish_to_social_helper_logs_error_in_english_on_http_failure(): void
    {
        config([
            'services.social_publisher.url' => 'https://social.example.test/publish',
            'services.social_publisher.connect_timeout' => 3,
            'services.social_publisher.timeout' => 9,
        ]);

        Log::spy();
        Http::fake([
            'https://social.example.test/publish' => Http::response(['error' => 'upstream down'], 500),
        ]);

        try {
            PublishToSocialHelper::publish('Título', 'Texto');
            $this->fail('HTTP 500 should throw exception.');
        } catch (\Exception $e) {
            $this->assertStringContainsString('500', $e->getMessage());
        }

        Log::shouldHaveReceived('error')
            ->withArgs(fn (string $message, array $context = []) => Str::contains($message, 'Social publisher returned non-successful HTTP status') && ($context['status'] ?? null) === 500)
            ->once();
    }
}
