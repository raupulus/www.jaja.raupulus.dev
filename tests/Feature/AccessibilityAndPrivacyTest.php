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
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AccessibilityAndPrivacyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Cache::flush();

        $this->ensureGeneralCategory();
    }

    public function test_public_layout_includes_skip_link_main_landmark_and_no_indefinite_smil_animations(): void
    {
        $html = $this->get(route('index'))->assertStatus(200)->getContent();

        $this->assertStringContainsString('<a href="#main-content" class="skip-link visually-hidden">Saltar al contenido principal</a>', $html);
        $this->assertStringContainsString('<main id="main-content" class="main-content" tabindex="-1">', $html);

        // Sin animaciones SMIL infinitas en el footer ni alt erróneos junto al texto de Discord
        $this->assertStringNotContainsString('<animateTransform', $html);
        $this->assertStringNotContainsString('alt="Bot de Discord de JaJa Project"', $html);
        $this->assertMatchesRegularExpression('/discord\.svg[^>]*\balt=""/s', $html);

        // Reglas globales CSS de accesibilidad
        $frontendCss = (string) file_get_contents(resource_path('css/frontend.css'));
        $buttonsCss = (string) file_get_contents(resource_path('css/buttons.css'));

        $this->assertStringContainsString(':focus-visible', $frontendCss);
        $this->assertStringContainsString('.visually-hidden', $frontendCss);
        $this->assertStringContainsString('prefers-reduced-motion', $frontendCss);
        $this->assertStringContainsString('prefers-reduced-motion', $buttonsCss);
    }

    public function test_random_category_and_group_links_do_not_use_inline_reload_or_div_inside_anchor(): void
    {
        $type = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes',
        ]);

        $group = Group::create([
            'type_id' => $type->id,
            'title' => 'Chistes Cortos',
            'slug' => 'chistes-cortos',
            'description' => 'Chistes cortos',
        ]);

        $user = User::factory()->create(['nick' => 'tester_a11y']);

        $content = Content::create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'title' => 'Chiste de prueba',
            'content' => 'Contenido de prueba',
            'is_adult' => false,
        ]);

        $category = Category::findOrFail(1);
        $category->contents()->syncWithoutDetaching([$content->id]);

        // Con <= 3 contenidos no se muestra el botón Aleatorio
        $groupHtmlSingle = $this->get(route('content.group.content.random', $group->slug))->assertStatus(200)->getContent();
        $this->assertStringNotContainsString('id="box-btn-random"', $groupHtmlSingle);

        $categoryHtmlSingle = $this->get(route('content.categoria.content.random', $category->slug))->assertStatus(200)->getContent();
        $this->assertStringNotContainsString('id="box-btn-random"', $categoryHtmlSingle);

        // Al superar los 3 contenidos mostrados, aparece el botón Aleatorio con parámetro de recarga
        for ($i = 2; $i <= 4; $i++) {
            $extra = Content::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'title' => "Chiste de prueba {$i}",
                'content' => "Contenido de prueba {$i}",
                'is_adult' => false,
            ]);
            $category->contents()->syncWithoutDetaching([$extra->id]);
        }

        $groupHtml = $this->get(route('content.group.content.random', $group->slug))->assertStatus(200)->getContent();
        $this->assertStringNotContainsString('onclick="window.location.reload()', $groupHtml);
        $this->assertStringNotContainsString('<div class="wheel">', $groupHtml);
        $this->assertStringContainsString('<span class="wheel" aria-hidden="true">', $groupHtml);
        $this->assertMatchesRegularExpression('/\/grupo\/chistes-cortos\/random\?r=[A-Za-z0-9]{6}#box-btn-random" rel="nofollow"/', $groupHtml);

        $categoryHtml = $this->get(route('content.categoria.content.random', $category->slug))->assertStatus(200)->getContent();
        $this->assertMatchesRegularExpression('/\/categoria\/general\/random\?r=[A-Za-z0-9]{6}#box-btn-random" rel="nofollow"/', $categoryHtml);
        $this->assertStringNotContainsString('onclick="window.location.reload()', $categoryHtml);
        $this->assertStringNotContainsString('<div class="wheel">', $categoryHtml);
        $this->assertStringContainsString('<span class="wheel" aria-hidden="true">', $categoryHtml);
    }

    public function test_suggestion_form_has_explicit_labels_keyboard_quiz_buttons_and_aria_error_states(): void
    {
        $type = Type::create([
            'name' => 'Quiz',
            'slug' => 'quiz',
            'description' => 'Preguntas quiz',
        ]);

        $homeHtml = $this->get(route('index'))->assertStatus(200)->getContent();

        // Todos los campos tienen label for= asociado y fieldset para quiz
        $expectedAssociations = [
            'suggestion-nick',
            'suggestion-type',
            'suggestion-group',
            'content-image',
            'suggestion-title',
            'suggestion-content',
            'suggestion-answer-1',
            'suggestion-answer-2',
            'suggestion-answer-3',
            'suggestion-answer-4',
            'terms',
        ];

        foreach ($expectedAssociations as $controlId) {
            $this->assertStringContainsString('id="'.$controlId.'"', $homeHtml);
            $this->assertStringContainsString('for="'.$controlId.'"', $homeHtml);
        }

        $this->assertStringContainsString('<label for="content-image" id="image-preview-container">', $homeHtml);
        $this->assertStringContainsString('<fieldset class="box-quiz-answers" id="quiz-answers-fieldset">', $homeHtml);
        $this->assertStringContainsString('<legend class="quiz-answers-legend">Respuestas del quiz</legend>', $homeHtml);

        // Selector de respuesta correcta basado en botones nativos con aria-pressed, nunca <span> clicables
        $this->assertStringContainsString('class="answer-check-btn', $homeHtml);
        $this->assertStringContainsString('aria-pressed="false"', $homeHtml);
        $this->assertStringNotContainsString('<span class="answer-check ', $homeHtml);

        // Tras error de validación del servidor se exponen aria-invalid="true", aria-describedby y role="alert"
        $errorResponse = $this->followingRedirects()
            ->from(route('index'))
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.150'])
            ->post(route('suggestion.send'), [
                'type_id' => $type->id,
                'title' => '',
                'content' => '',
            ]);

        $errorHtml = $errorResponse->assertStatus(200)->getContent();
        $this->assertStringContainsString('aria-invalid="true"', $errorHtml);
        $this->assertStringContainsString('aria-describedby="error-title"', $errorHtml);
        $this->assertStringContainsString('aria-describedby="error-content"', $errorHtml);
        $this->assertStringContainsString('id="error-title" class="field-error" role="alert"', $errorHtml);
        $this->assertStringContainsString('<div class="alert alert-danger" role="alert">', $errorHtml);
    }

    public function test_flash_alerts_expose_proper_aria_live_roles(): void
    {
        $successHtml = $this->withSession(['success' => 'Operación completada'])
            ->get(route('index'))
            ->assertStatus(200)
            ->getContent();
        $this->assertStringContainsString('<div class="main-alert alert alert-success" role="status" aria-live="polite">', $successHtml);

        $errorHtml = $this->withSession(['error' => 'Error de prueba'])
            ->get(route('index'))
            ->assertStatus(200)
            ->getContent();
        $this->assertStringContainsString('<div class="main-alert alert alert-danger" role="alert">', $errorHtml);
    }

    public function test_privacy_lazy_recaptcha_rgpd_notice_cookies_escape_and_secure_session_env(): void
    {
        config(['google.recaptcha.site_key' => 'test-public-site-key']);

        $user = User::factory()->create(['nick' => 'privacy_user']);
        $collaborator = Collaborator::create([
            'user_id' => $user->id,
            'name' => 'Ada Lovelace',
            'nick' => 'ada_l',
            'description' => 'Colaboradora',
        ]);
        CollaboratorProject::create([
            'collaborator_id' => $collaborator->id,
            'title' => 'Motor Analítico',
            'slug' => 'motor-analitico',
            'excerpt' => 'Resumen',
            'content' => 'Contenido',
            'type' => 'web',
            'repository_type' => 'github',
            'status' => 'published',
        ]);
        Page::create([
            'title' => 'Política de Privacidad',
            'slug' => 'politica-de-privacidad',
            'excerpt' => 'Privacidad',
            'content' => 'Texto legal',
            'status' => 'published',
        ]);

        // Ninguna vista debe cargar eager el script externo de Google reCAPTCHA al visitar la página
        $routes = [
            route('index'),
            route('content.groups.index'),
            route('content.categories.index'),
            route('page.index'),
            route('collaborator.index'),
        ];

        foreach ($routes as $url) {
            $html = $this->get($url)->assertStatus(200)->getContent();
            $this->assertStringNotContainsString('https://www.google.com/recaptcha/api.js', $html);
        }

        // La portada expone data-recaptcha-site-key para carga bajo demanda, nota RGPD y banner accesible
        $homeHtml = $this->get(route('index'))->assertStatus(200)->getContent();
        $this->assertStringContainsString('data-recaptcha-site-key="test-public-site-key"', $homeHtml);
        $this->assertStringContainsString('Información básica sobre protección de datos:', $homeHtml);
        $this->assertStringContainsString('public@raupulus.dev', $homeHtml);
        $this->assertStringContainsString('role="dialog"', $homeHtml);
        $this->assertStringContainsString('id="acceptCookies"', $homeHtml);
        $this->assertStringContainsString('id="dismissCookies"', $homeHtml);

        // Escape en cookies.js debe ocultar el banner sin aceptar cookies
        $cookiesJs = (string) file_get_contents(resource_path('js/cookies.js'));
        $this->assertMatchesRegularExpression(
            "/if\s*\(e\.key\s*===\s*'Escape'[^}]*this\.hideBanner\(\)/s",
            $cookiesJs
        );
        $this->assertDoesNotMatchRegularExpression(
            "/if\s*\(e\.key\s*===\s*'Escape'[^}]*this\.acceptCookies\(\)/s",
            $cookiesJs
        );

        // .env.example.production configura cookie de sesión segura
        $envProd = (string) file_get_contents(base_path('.env.example.production'));
        $this->assertStringContainsString('SESSION_SECURE_COOKIE=true', $envProd);
    }
}
