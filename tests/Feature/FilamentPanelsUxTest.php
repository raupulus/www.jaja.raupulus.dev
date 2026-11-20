<?php

namespace Tests\Feature;

use App\Filament\Admin\Pages\Dashboard as AdminDashboard;
use App\Filament\Admin\Resources\CollaboratorResource;
use App\Filament\Admin\Resources\SuggestionResource;
use App\Filament\Admin\Resources\UserResource\Pages\EditUser;
use App\Filament\Admin\Widgets\ContentOverview;
use App\Filament\Admin\Widgets\GroupOverview;
use App\Filament\Admin\Widgets\SuggestionOverview;
use App\Filament\Admin\Widgets\UserContributorsChart;
use App\Filament\Admin\Widgets\UserOverview;
use App\Models\Category;
use App\Models\Collaborator;
use App\Models\Content;
use App\Models\Group;
use App\Models\Page;
use App\Models\Report;
use App\Models\Suggestion;
use App\Models\Type;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentPanelsUxTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

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

        $this->admin = User::factory()->create([
            'role_id' => 1,
            'nick' => 'panel_admin',
        ]);

        $this->regularUser = User::factory()->create([
            'role_id' => 2,
            'nick' => 'panel_user',
        ]);
    }

    public function test_all_admin_and_panel_resource_indexes_render_with_seeded_records(): void
    {
        $type = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes',
        ]);

        $group = Group::create([
            'type_id' => $type->id,
            'title' => 'Grupo Panel',
            'slug' => 'grupo-panel',
            'description' => 'Descripción',
        ]);

        $content = Content::create([
            'user_id' => $this->regularUser->id,
            'group_id' => $group->id,
            'title' => 'Contenido de prueba',
            'content' => 'Texto largo para verificar la tabla del panel',
            'is_adult' => false,
        ]);

        Collaborator::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Ada Lovelace',
            'nick' => 'ada_lovelace',
            'description' => 'Colaboradora open source',
        ]);

        Page::create([
            'title' => 'Acerca de',
            'slug' => 'about',
            'excerpt' => 'Resumen acerca de',
            'content' => 'Contenido markdown',
            'status' => 'published',
        ]);

        Suggestion::create([
            'type_id' => $type->id,
            'group_id' => $group->id,
            'title' => 'Sugerencia pendiente',
            'content' => 'Texto de sugerencia',
        ]);

        Report::create([
            'user_id' => $this->regularUser->id,
            'reporter_name' => 'Informante Test',
            'reportable_type' => Content::class,
            'reportable_id' => $content->id,
            'type' => 'spam',
            'priority' => 'medium',
            'status' => 'pending',
            'description' => 'Descripción del reporte',
        ]);

        $adminRoutes = [
            '/admin',
            '/admin/categories',
            '/admin/collaborators',
            '/admin/contents',
            '/admin/groups',
            '/admin/pages',
            '/admin/reports',
            '/admin/suggestions',
            '/admin/types',
            '/admin/users',
        ];

        foreach ($adminRoutes as $uri) {
            $this->actingAs($this->admin)
                ->get($uri)
                ->assertStatus(200);
        }

        $panelRoutes = [
            '/panel',
            '/panel/collaborators',
            '/panel/contents',
        ];

        foreach ($panelRoutes as $uri) {
            $this->actingAs($this->regularUser)
                ->followingRedirects()
                ->get($uri)
                ->assertStatus(200);
        }

        // La navegación cruzada expone acceso a web pública y entre paneles según rol
        $adminHtml = $this->actingAs($this->admin)->get('/admin')->assertStatus(200)->getContent();
        $this->assertStringContainsString('Ver web pública', $adminHtml);
        $this->assertStringContainsString('Ir a mi Panel', $adminHtml);

        $panelHtml = $this->actingAs($this->regularUser)->get('/panel')->assertStatus(200)->getContent();
        $this->assertStringContainsString('Ver web pública', $panelHtml);
        $this->assertStringNotContainsString('Panel de Administración', $panelHtml);
    }

    public function test_collaborator_resource_uses_name_for_global_search_and_suggestion_uses_moderation_group(): void
    {
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Collaborator::create([
            'user_id' => $this->regularUser->id,
            'name' => 'Grace Hopper',
            'nick' => 'grace_h',
            'description' => 'Compiladores',
        ]);

        $this->assertSame('name', CollaboratorResource::getRecordTitleAttribute());
        $results = CollaboratorResource::getGlobalSearchResults('Grace');
        $this->assertCount(1, $results);

        $this->assertSame('Moderación', SuggestionResource::getNavigationGroup());
        $this->assertSame('title', SuggestionResource::getRecordTitleAttribute());
    }

    public function test_admin_dashboard_maintenance_actions_require_confirmation_and_update_stats_safely(): void
    {
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $component = Livewire::test(AdminDashboard::class)
            ->assertActionExists('generate_sitemap')
            ->assertActionExists('update_stats')
            ->callAction('update_stats')
            ->assertNotified('Estadísticas actualizadas');

        $instance = $component->instance();
        $reflection = new \ReflectionMethod($instance, 'getHeaderActions');
        $reflection->setAccessible(true);
        /** @var array<int, Action> $actions */
        $actions = $reflection->invoke($instance);

        foreach ($actions as $action) {
            $this->assertTrue($action->shouldOpenModal(), "Action {$action->getName()} must require confirmation modal");
        }
    }

    public function test_admin_can_assign_role_id_via_edit_user_while_protecting_last_admin(): void
    {
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        // role_id sigue fuera de $fillable para evitar mass-assignment
        $this->assertNotContains('role_id', (new User)->getFillable());

        // Promover usuario normal a administrador
        Livewire::test(EditUser::class, ['record' => $this->regularUser->getRouteKey()])
            ->fillForm([
                'name' => $this->regularUser->name,
                'email' => $this->regularUser->email,
                'nick' => $this->regularUser->nick,
                'role_id' => 1,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(1, (int) $this->regularUser->fresh()->role_id);

        // Con dos administradores es posible devolver al segundo a usuario regular
        Livewire::test(EditUser::class, ['record' => $this->regularUser->getRouteKey()])
            ->fillForm([
                'name' => $this->regularUser->name,
                'email' => $this->regularUser->email,
                'nick' => $this->regularUser->nick,
                'role_id' => 2,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(2, (int) $this->regularUser->fresh()->role_id);

        // No se puede rebajar al único administrador activo restante
        Livewire::test(EditUser::class, ['record' => $this->admin->getRouteKey()])
            ->fillForm([
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'nick' => $this->admin->nick,
                'role_id' => 2,
            ])
            ->call('save')
            ->assertHasFormErrors(['role_id']);

        $this->assertSame(1, (int) $this->admin->fresh()->role_id);
    }

    public function test_widgets_and_resources_define_fixed_palette_responsive_columns_and_action_groups(): void
    {
        $type = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes',
        ]);
        $group = Group::create([
            'type_id' => $type->id,
            'title' => 'General',
            'slug' => 'general-group',
            'description' => 'General',
        ]);
        Content::create([
            'user_id' => $this->regularUser->id,
            'group_id' => $group->id,
            'title' => 'Aporte',
            'content' => 'Texto',
            'uploaded_by' => $this->regularUser->nick,
            'is_adult' => false,
        ]);

        Cache::flush();

        $chart = new UserContributorsChart;
        $method = new \ReflectionMethod($chart, 'getData');
        $method->setAccessible(true);
        $chartData = $method->invoke($chart);

        $this->assertSame(UserContributorsChart::PALETTE[0], $chartData['datasets'][0]['backgroundColor'][0] ?? null);

        $pageResource = (string) file_get_contents(app_path('Filament/Admin/Resources/PageResource.php'));
        $suggestionResource = (string) file_get_contents(app_path('Filament/Admin/Resources/SuggestionResource.php'));
        $adminContentResource = (string) file_get_contents(app_path('Filament/Admin/Resources/ContentResource.php'));
        $reportResource = (string) file_get_contents(app_path('Filament/Admin/Resources/ReportResource.php'));

        $this->assertStringContainsString("columns(['default' => 1, 'lg' => 3])", $pageResource);
        $this->assertStringContainsString('->live()', $suggestionResource);
        $this->assertStringContainsString('ActionGroup::make', $adminContentResource);
        $this->assertStringContainsString('ActionGroup::make', $reportResource);

        foreach ([
            ContentOverview::class,
            SuggestionOverview::class,
            GroupOverview::class,
            UserOverview::class,
            \App\Filament\Panel\Widgets\ContentOverview::class,
        ] as $widgetClass) {
            $reflection = new \ReflectionClass($widgetClass);
            $sortProp = $reflection->getProperty('sort');
            $sortProp->setAccessible(true);
            $spanProp = $reflection->getProperty('columnSpan');
            $spanProp->setAccessible(true);

            $this->assertNotNull($sortProp->getValue(null), "Widget {$widgetClass} must declare \$sort");
            $this->assertSame('full', $spanProp->getValue(new $widgetClass), "Widget {$widgetClass} must use full columnSpan");
        }
    }
}
