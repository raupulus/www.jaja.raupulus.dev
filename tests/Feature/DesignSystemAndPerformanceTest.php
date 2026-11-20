<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignSystemAndPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_head_includes_color_scheme_font_preloads_and_lcp_image_attributes(): void
    {
        $html = $this->withoutVite()->get(route('index'))->assertStatus(200)->getContent();

        $this->assertStringContainsString('<meta name="color-scheme" content="dark">', $html);
        $this->assertStringContainsString('fonts/inter/inter-400.woff2', $html);
        $this->assertStringContainsString('fonts/inter/inter-700.woff2', $html);

        // No debe haber etiquetas <style> inline renderizadas en la vista pública
        $this->assertStringNotContainsString('<style>', $html);

        // No debe referenciar iconos externos en raw.githubusercontent.com
        $this->assertStringNotContainsString('raw.githubusercontent.com', $html);

        // La imagen LCP de portada lleva fetchpriority="high" y dimensiones explícitas sin loading="lazy"
        $this->assertMatchesRegularExpression('/<img[^>]*portada-jaja-project\.webp[^>]*fetchpriority="high"/s', $html);
        $this->assertMatchesRegularExpression('/<img[^>]*portada-jaja-project\.webp[^>]*width="800"[^>]*height="300"/s', $html);
        $this->assertDoesNotMatchRegularExpression('/<img[^>]*portada-jaja-project\.webp[^>]*loading="lazy"/s', $html);
    }

    public function test_vite_manifest_is_valid_and_excludes_removed_entries(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $this->assertFileExists($manifestPath);

        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        $this->assertIsArray($manifest);

        $this->assertArrayNotHasKey('resources/css/home.css', $manifest);
        $this->assertArrayNotHasKey('resources/css/pages.css', $manifest);
        $this->assertArrayNotHasKey('resources/css/app.css', $manifest);
        $this->assertArrayNotHasKey('resources/js/app.js', $manifest);
        $this->assertArrayHasKey('resources/css/frontend.css', $manifest);
    }

    public function test_android_chrome_512_favicon_is_compressed_under_80kb(): void
    {
        $iconPath = public_path('images/favicon/android-chrome-512x512.png');
        $this->assertFileExists($iconPath);
        $this->assertLessThan(80 * 1024, filesize($iconPath));
    }

    public function test_pages_index_omits_empty_card_image_container_when_page_has_no_image(): void
    {
        Page::create([
            'title' => 'Página sin imagen',
            'slug' => 'pagina-sin-imagen',
            'excerpt' => 'Resumen de página sin imagen.',
            'content' => 'Contenido de prueba.',
            'status' => 'published',
        ]);

        $html = $this->withoutVite()->get(route('page.index'))->assertStatus(200)->getContent();

        $this->assertStringContainsString('card-content--no-image', $html);
        $this->assertStringNotContainsString('<div class="card-content-image">', $html);
    }
}
