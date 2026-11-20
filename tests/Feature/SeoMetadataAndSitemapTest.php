<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collaborator;
use App\Models\CollaboratorProject;
use App\Models\Content;
use App\Models\Group;
use App\Models\Page;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SeoMetadataAndSitemapTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Type $type;

    private Group $group;

    private Category $category;

    private Page $page;

    private Collaborator $collaborator;

    private CollaboratorProject $project;

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

        $this->user = User::factory()->create([
            'nick' => 'seo_tester',
        ]);

        $this->type = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes variados para reír en comunidad.',
        ]);

        $this->group = Group::create([
            'type_id' => $this->type->id,
            'title' => 'Chistes Informáticos',
            'slug' => 'chistes-informaticos',
            'description' => 'Selección de chistes sobre programación, redes y desarrollo.',
        ]);

        $this->category = Category::create([
            'title' => 'PHP',
            'slug' => 'php',
            'description' => 'Humor sobre PHP, Laravel y desarrollo backend.',
        ]);

        $content = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'title' => 'Chiste SEO',
            'content' => 'Un bug entra a un bar y pide una cerveza.',
            'is_adult' => false,
        ]);

        $this->category->contents()->sync([$content->id]);

        $this->page = Page::create([
            'title' => 'Guía de la API',
            'slug' => 'guia-api',
            'excerpt' => 'Documentación y guía práctica para integrar la API de comunidad.',
            'content' => '## Uso básico\nConsulta el endpoint aleatorio.',
            'keywords' => 'api, guia, bots',
            'status' => 'published',
        ]);

        $this->collaborator = Collaborator::create([
            'user_id' => $this->user->id,
            'name' => 'Ada Lovelace',
            'nick' => 'ada_dev',
            'description' => 'Desarrolladora colaboradora de bots y herramientas abiertas.',
            'website' => 'https://example.com',
        ]);

        $this->project = CollaboratorProject::create([
            'collaborator_id' => $this->collaborator->id,
            'title' => 'Bot Comunidad',
            'slug' => 'bot-comunidad',
            'excerpt' => 'Bot de Discord para compartir chistes y quiz.',
            'content' => '## Características\nComandos automáticos.',
            'type' => 'bot',
            'repository_type' => 'github',
            'keywords' => 'discord, bot, humor',
            'status' => 'published',
        ]);
    }

    /**
     * Extrae todos los bloques JSON-LD de un HTML y verifica que sean válidos.
     *
     * @return array<int, array<string, mixed>>
     */
    private function extractJsonLdBlocks(string $html): array
    {
        preg_match_all('/<script\s+type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);
        $this->assertNotEmpty($matches[1], 'No se encontró ningún bloque JSON-LD en la vista.');

        $decoded = [];
        foreach ($matches[1] as $rawJson) {
            $data = json_decode($rawJson, true);
            $this->assertSame(JSON_ERROR_NONE, json_last_error(), 'JSON-LD inválido: '.json_last_error_msg());
            $this->assertSame('https://schema.org', $data['@context'] ?? null);
            $decoded[] = $data;
        }

        return $decoded;
    }

    public function test_all_public_views_emit_valid_json_ld(): void
    {
        $routesAndExpectedTypes = [
            route('index') => ['WebSite', 'Organization'],
            route('content.types.index') => ['CollectionPage', 'BreadcrumbList'],
            route('content.groups.index') => ['CollectionPage', 'BreadcrumbList'],
            route('content.type.group.index', $this->type->slug) => ['CollectionPage', 'BreadcrumbList'],
            route('content.categories.index') => ['CollectionPage', 'BreadcrumbList'],
            route('page.index') => ['CollectionPage', 'BreadcrumbList'],
            route('page.show', $this->page->slug) => ['WebPage', 'BreadcrumbList'],
            route('collaborator.index') => ['CollectionPage', 'BreadcrumbList'],
            route('collaborator.show', $this->collaborator->nick) => ['ProfilePage', 'BreadcrumbList'],
            route('collaborator.project.show', [$this->collaborator->nick, $this->project->slug]) => ['SoftwareApplication', 'BreadcrumbList'],
            route('content.group.content.random', $this->group->slug) => ['CollectionPage', 'BreadcrumbList'],
            route('content.categoria.content.random', $this->category->slug) => ['CollectionPage', 'BreadcrumbList'],
        ];

        foreach ($routesAndExpectedTypes as $url => $expectedTypes) {
            $response = $this->get($url);
            $response->assertStatus(200);

            $blocks = $this->extractJsonLdBlocks($response->getContent());
            $foundTypes = array_column($blocks, '@type');

            foreach ($expectedTypes as $expectedType) {
                $this->assertContains($expectedType, $foundTypes, "Falta @type {$expectedType} en {$url}");
            }
        }
    }

    public function test_groups_and_groups_by_type_have_unique_title_heading_and_meta_description(): void
    {
        $allGroupsHtml = $this->get(route('content.groups.index'))->assertStatus(200)->getContent();
        $typedGroupsHtml = $this->get(route('content.type.group.index', $this->type->slug))->assertStatus(200)->getContent();

        preg_match('/<title>(.*?)<\/title>/', $allGroupsHtml, $allTitle);
        preg_match('/<title>(.*?)<\/title>/', $typedGroupsHtml, $typedTitle);

        preg_match('/<meta\s+name="description"\s+content="(.*?)"/', $allGroupsHtml, $allDesc);
        preg_match('/<meta\s+name="description"\s+content="(.*?)"/', $typedGroupsHtml, $typedDesc);

        preg_match('/<h1[^>]*>(.*?)<\/h1>/', $allGroupsHtml, $allH1);
        preg_match('/<h1[^>]*>(.*?)<\/h1>/', $typedGroupsHtml, $typedH1);

        $this->assertNotEmpty($allTitle[1] ?? '');
        $this->assertNotEmpty($typedTitle[1] ?? '');
        $this->assertNotSame($allTitle[1], $typedTitle[1]);

        $this->assertNotEmpty($allDesc[1] ?? '');
        $this->assertNotEmpty($typedDesc[1] ?? '');
        $this->assertNotSame($allDesc[1], $typedDesc[1]);

        $this->assertNotSame($allH1[1] ?? '', $typedH1[1] ?? '');
        $this->assertStringContainsString('Grupos de Chistes', $typedH1[1] ?? '');
    }

    public function test_head_emits_jpg_og_defaults_dimensions_twitter_alt_and_og_types(): void
    {
        $homeHtml = $this->get(route('index'))->assertStatus(200)->getContent();
        $this->assertStringContainsString('images/social/jaja-project-og-default.jpg', $homeHtml);
        $this->assertStringContainsString('images/social/jaja-project-twitter-default.jpg', $homeHtml);
        $this->assertStringContainsString('<meta property="og:image:width" content="1200">', $homeHtml);
        $this->assertStringContainsString('<meta property="og:image:height" content="630">', $homeHtml);
        $this->assertStringContainsString('<meta name="twitter:image:alt"', $homeHtml);
        $this->assertStringContainsString('<meta property="og:type" content="website">', $homeHtml);

        $pageHtml = $this->get(route('page.show', $this->page->slug))->assertStatus(200)->getContent();
        $this->assertStringContainsString('<meta property="og:type" content="article">', $pageHtml);

        $collabHtml = $this->get(route('collaborator.show', $this->collaborator->nick))->assertStatus(200)->getContent();
        $this->assertStringContainsString('<meta property="og:type" content="profile">', $collabHtml);
    }

    public function test_views_follow_sequential_heading_hierarchy_without_h1_to_h3_jump(): void
    {
        $urls = [
            route('content.types.index'),
            route('content.groups.index'),
            route('content.categories.index'),
            route('page.index'),
            route('content.group.content.random', $this->group->slug),
            route('content.categoria.content.random', $this->category->slug),
        ];

        foreach ($urls as $url) {
            $html = $this->get($url)->assertStatus(200)->getContent();
            $mainPos = strpos($html, '<main');
            $mainHtml = $mainPos !== false ? substr($html, $mainPos) : $html;

            preg_match_all('/<(h[1-6])\b/i', $mainHtml, $matches);
            $levels = array_map(fn (string $tag) => (int) substr($tag, 1), $matches[1]);

            $this->assertNotEmpty($levels, "No se encontraron encabezados en {$url}");
            $this->assertSame(1, $levels[0], "El primer encabezado debe ser h1 en {$url}");

            for ($i = 1; $i < count($levels); $i++) {
                $this->assertLessThanOrEqual(
                    $levels[$i - 1] + 1,
                    $levels[$i],
                    "Salto de encabezado de h{$levels[$i - 1]} a h{$levels[$i]} en {$url}"
                );
            }
        }
    }

    public function test_sitemap_generate_includes_dynamic_types_and_excludes_json_yaml(): void
    {
        Type::create([
            'name' => 'Colmos',
            'slug' => 'colmos',
            'description' => 'Tipo dinámico de prueba.',
        ]);

        $tempPath = storage_path('framework/testing-sitemap.xml');

        try {
            $exitCode = Artisan::call('sitemap:generate', [
                '--force' => true,
                '--output' => $tempPath,
            ]);

            $this->assertSame(0, $exitCode);
            $this->assertFileExists($tempPath);

            $xmlContent = file_get_contents($tempPath);
            $this->assertNotFalse($xmlContent);

            $xml = simplexml_load_string($xmlContent);
            $this->assertNotFalse($xml);

            $locs = [];
            foreach ($xml->url as $urlNode) {
                $locs[] = (string) $urlNode->loc;
            }

            $this->assertContains(route('index'), $locs);
            $this->assertContains(route('content.type.group.index', 'colmos'), $locs);
            $this->assertContains(route('page.show', $this->page->slug), $locs);
            $this->assertContains(route('collaborator.project.show', [$this->collaborator->nick, $this->project->slug]), $locs);

            foreach ($locs as $loc) {
                $this->assertStringNotContainsString('collection.json', $loc);
                $this->assertStringNotContainsString('openapi.yaml', $loc);
            }
        } finally {
            @unlink($tempPath);
            @unlink($tempPath.'.bak');
        }
    }

    public function test_robots_and_llms_files_follow_canonical_domain_and_public_routes(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));
        $this->assertStringContainsString('Sitemap: https://jaja.raupulus.dev/sitemap.xml', $robots);
        $this->assertStringNotContainsString('www.jaja.raupulus.dev', $robots);
        $this->assertStringNotContainsString('Crawl-delay', $robots);

        $llms = file_get_contents(public_path('llms.txt'));
        $this->assertStringNotContainsString('/admin', $llms);
        $this->assertStringNotContainsString('/panel', $llms);
        $this->assertStringNotContainsString('/api/random', $llms);
        $this->assertStringNotContainsString('/chiste/random', $llms);
        $this->assertStringContainsString('/api/v1/random', $llms);
    }
}
