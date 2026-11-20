<?php

namespace Tests\Feature;

use App\Helpers\IpHelper;
use App\Http\Middleware\VerifyRecaptcha;
use App\Models\Category;
use App\Models\Content;
use App\Models\Group;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

class ReliabilityAndArchitectureTest extends TestCase
{
    use RefreshDatabase;

    private Type $chistesType;

    private Type $quizType;

    private Group $chistesGroup;

    private Group $quizGroup;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Cache::flush();

        Category::create([
            'id' => 1,
            'title' => 'General',
            'slug' => 'general',
            'description' => 'Categoría general',
        ]);

        $this->user = User::factory()->create([
            'role_id' => 2,
            'nick' => 'arch_tester',
        ]);

        $this->chistesType = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes',
        ]);

        $this->quizType = Type::create([
            'name' => 'Quiz',
            'slug' => 'quiz',
            'description' => 'Quiz',
        ]);

        $this->chistesGroup = Group::create([
            'type_id' => $this->chistesType->id,
            'title' => 'Chistes General',
            'slug' => 'chistes-general',
            'description' => 'Grupo chistes',
        ]);

        $this->quizGroup = Group::create([
            'type_id' => $this->quizType->id,
            'title' => 'Quiz General',
            'slug' => 'quiz-general',
            'description' => 'Grupo quiz',
        ]);
    }

    public function test_content_public_safe_scope_and_helper_respect_config_and_adult_flag(): void
    {
        DB::table('groups')->insert([
            'id' => 4,
            'type_id' => $this->chistesType->id,
            'title' => 'Grupo Excluido',
            'slug' => 'grupo-excluido',
            'description' => 'Excluido por configuración',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $excludedGroup = Group::findOrFail(4);

        $safeContent = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->chistesGroup->id,
            'title' => 'Contenido público seguro',
            'content' => 'Texto apto',
            'is_adult' => false,
        ]);

        $adultContent = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->chistesGroup->id,
            'title' => 'Contenido adulto',
            'content' => 'Texto adulto',
            'is_adult' => true,
        ]);

        $excludedGroupContent = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $excludedGroup->id,
            'title' => 'Contenido grupo excluido',
            'content' => 'Texto de grupo excluido',
            'is_adult' => false,
        ]);

        $this->assertTrue($safeContent->isPublicSafe());
        $this->assertFalse($adultContent->isPublicSafe());
        $this->assertFalse($excludedGroupContent->isPublicSafe());

        $publicIds = Content::publicSafe()->pluck('id')->all();
        $this->assertSame([$safeContent->id], $publicIds);

        // Respeta override por configuración sin tocar constantes
        config(['content.excluded_public_group_ids' => [$this->chistesGroup->id]]);

        $this->assertFalse($safeContent->isPublicSafe());
        $this->assertTrue($excludedGroupContent->isPublicSafe());
        $this->assertSame([$excludedGroupContent->id], Content::publicSafe()->pluck('id')->all());
    }

    public function test_suggestion_request_validates_group_id_coherence_with_type_id(): void
    {
        $this->withoutMiddleware(VerifyRecaptcha::class);

        // Enviar type_id=chistes con un group_id de quiz debe fallar
        $invalidResponse = $this->post(route('suggestion.send'), [
            'type_id' => $this->chistesType->id,
            'group_id' => $this->quizGroup->id,
            'nick' => 'coherence_user',
            'title' => 'Título incoherente',
            'content' => 'Contenido con grupo de otro tipo.',
            'terms' => '1',
        ]);
        $invalidResponse->assertSessionHasErrors('group_id');
        $this->assertDatabaseCount('suggestions', 0);

        RateLimiter::clear('suggestions:127.0.0.1');

        // Enviar type_id=chistes con group_id de chistes debe guardarse
        $validResponse = $this->post(route('suggestion.send'), [
            'type_id' => $this->chistesType->id,
            'group_id' => $this->chistesGroup->id,
            'nick' => 'coherence_user',
            'title' => 'Título coherente',
            'content' => 'Contenido con grupo correcto.',
            'terms' => '1',
        ]);
        $validResponse->assertRedirect(route('index'))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseCount('suggestions', 1);
    }

    public function test_ip_helper_ignores_spoofed_headers_unless_proxy_is_trusted(): void
    {
        TrustProxies::flushState();
        config(['trustedproxy.proxies' => null]);

        $trustProxies = new TrustProxies();

        $untrustedRequest = Request::create('/api/v1/random', 'GET', [], [], [], [
            'REMOTE_ADDR' => '198.51.100.10',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.50',
            'HTTP_CF_CONNECTING_IP' => '203.0.113.51',
        ]);

        $trustProxies->handle($untrustedRequest, static fn (Request $req) => response('ok'));
        $this->assertSame('198.51.100.10', IpHelper::getClientIp($untrustedRequest));

        // Ahora marcamos 198.51.100.10 como proxy confiable
        config(['trustedproxy.proxies' => '198.51.100.10']);

        $trustedCfRequest = Request::create('/api/v1/random', 'GET', [], [], [], [
            'REMOTE_ADDR' => '198.51.100.10',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.50',
            'HTTP_CF_CONNECTING_IP' => '203.0.113.51',
        ]);
        $trustProxies->handle($trustedCfRequest, static fn (Request $req) => response('ok'));
        $this->assertSame('203.0.113.51', IpHelper::getClientIp($trustedCfRequest));

        $trustedXffRequest = Request::create('/api/v1/random', 'GET', [], [], [], [
            'REMOTE_ADDR' => '198.51.100.10',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.50',
        ]);
        $trustProxies->handle($trustedXffRequest, static fn (Request $req) => response('ok'));
        $this->assertSame('203.0.113.50', IpHelper::getClientIp($trustedXffRequest));

        TrustProxies::flushState();
    }

    public function test_artisan_schedule_list_runs_and_console_tasks_prevent_overlapping(): void
    {
        $exitCode = Artisan::call('schedule:list');
        $output = Artisan::output();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('stats:update', $output);
        $this->assertStringContainsString('sitemap:generate', $output);
        $this->assertStringContainsString('content:publish-social', $output);

        $consoleRoutes = (string) file_get_contents(base_path('routes/console.php'));
        $this->assertSame(5, substr_count($consoleRoutes, '->withoutOverlapping()'));
    }

    public function test_all_log_calls_use_english_and_no_hardcoded_excluded_group_queries_remain(): void
    {
        $finder = (new Finder())->files()->in(app_path())->name('*.php');

        $spanishLogPatterns = [
            'Error en ',
            'Error al ',
            'Error durante',
            'No se pudo',
            '$request->all()',
        ];

        foreach ($finder as $file) {
            $content = $file->getContents();
            $relativePath = $file->getRelativePathname();

            foreach ($spanishLogPatterns as $forbidden) {
                if (preg_match('/Log::(?:debug|info|notice|warning|error|critical|alert|emergency)\s*\([^\n]*' . preg_quote($forbidden, '/') . '/u', $content) === 1) {
                    $this->fail("Found disallowed log pattern '{$forbidden}' in app/{$relativePath}");
                }
            }

            if ($relativePath !== 'Models/Content.php') {
                $this->assertDoesNotMatchRegularExpression(
                    '/whereNotIn\([^)]*\[4,\s*14\]|in_array\([^)]*\[4,\s*14\]/',
                    $content,
                    "Found hardcoded [4, 14] group exclusion outside Content model in app/{$relativePath}"
                );
            }
        }

        $this->addToAssertionCount(1);
    }
}
