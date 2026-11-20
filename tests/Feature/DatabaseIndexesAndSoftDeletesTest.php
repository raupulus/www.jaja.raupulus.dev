<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\CategoryResource\Pages\CreateCategory;
use App\Models\Category;
use App\Models\Type;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class DatabaseIndexesAndSoftDeletesTest extends TestCase
{
    use RefreshDatabase;

    public function test_additive_foreign_key_and_partial_indexes_exist_in_schema(): void
    {
        $expectedIndexes = [
            'content_categories' => ['content_categories_content_id_category_id_index', 'content_categories_category_id_index'],
            'content_options' => ['content_options_content_id_order_index'],
            'suggestions' => ['suggestions_type_id_index', 'suggestions_group_id_index', 'suggestions_approved_at_index'],
            'suggestion_options' => ['suggestion_options_suggestion_id_order_index'],
            'suggestion_categories' => ['suggestion_categories_suggestion_id_category_id_index', 'suggestion_categories_category_id_index'],
            'collaborators' => ['collaborators_user_id_index'],
            'collaborator_projects' => ['collaborator_projects_collaborator_id_status_index'],
            'reports' => ['reports_user_id_index', 'reports_assigned_to_index'],
            'contents' => ['contents_is_adult_group_id_index'],
            'users' => ['users_email_unique_active'],
            'categories' => ['categories_slug_unique_active'],
            'types' => ['types_slug_unique_active'],
            'groups' => ['groups_slug_unique_active'],
            'pages' => ['pages_slug_unique_active'],
        ];

        foreach ($expectedIndexes as $table => $indexNames) {
            $actualIndexes = Schema::getIndexListing($table);

            foreach ($indexNames as $indexName) {
                $this->assertContains(
                    $indexName,
                    $actualIndexes,
                    "Expected index {$indexName} to exist on table {$table}"
                );
            }
        }
    }

    public function test_soft_deleted_records_allow_reusing_email_and_slug_while_active_duplicates_fail(): void
    {
        $deletedUser = User::factory()->create([
            'email' => 'reusable@raupulus.dev',
            'nick' => 'deleted_one',
        ]);
        $deletedUser->delete();

        $activeUser = User::factory()->create([
            'email' => 'reusable@raupulus.dev',
            'nick' => 'active_one',
        ]);
        $this->assertNotSame($deletedUser->id, $activeUser->id);

        try {
            User::factory()->create([
                'email' => 'reusable@raupulus.dev',
                'nick' => 'duplicate_active',
            ]);
            $this->fail('Duplicate active email should violate unique partial index');
        } catch (QueryException $e) {
            $this->addToAssertionCount(1);
        }

        $deletedCategory = Category::create([
            'title' => 'Categoría vieja',
            'slug' => 'cat-reusable',
            'description' => 'Antigua',
        ]);
        $deletedCategory->delete();

        $activeCategory = Category::create([
            'title' => 'Categoría nueva',
            'slug' => 'cat-reusable',
            'description' => 'Nueva',
        ]);
        $this->assertNotSame($deletedCategory->id, $activeCategory->id);

        try {
            Category::create([
                'title' => 'Categoría duplicada',
                'slug' => 'cat-reusable',
                'description' => 'Duplicada',
            ]);
            $this->fail('Duplicate active category slug should violate unique partial index');
        } catch (QueryException $e) {
            $this->addToAssertionCount(1);
        }

        $deletedType = Type::create([
            'name' => 'Tipo viejo',
            'slug' => 'tipo-reusable',
            'description' => 'Antiguo',
        ]);
        $deletedType->delete();

        $activeType = Type::create([
            'name' => 'Tipo nuevo',
            'slug' => 'tipo-reusable',
            'description' => 'Nuevo',
        ]);
        $this->assertNotSame($deletedType->id, $activeType->id);

        try {
            Type::create([
                'name' => 'Tipo duplicado',
                'slug' => 'tipo-reusable',
                'description' => 'Duplicado',
            ]);
            $this->fail('Duplicate active type slug should violate unique partial index');
        } catch (QueryException $e) {
            $this->addToAssertionCount(1);
        }

        DB::table('groups')->insert([
            'type_id' => $activeType->id,
            'title' => 'Grupo borrado',
            'slug' => 'grupo-reusable',
            'description' => 'Antiguo',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => now(),
        ]);

        DB::table('groups')->insert([
            'type_id' => $activeType->id,
            'title' => 'Grupo activo',
            'slug' => 'grupo-reusable',
            'description' => 'Activo',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        try {
            DB::table('groups')->insert([
                'type_id' => $activeType->id,
                'title' => 'Grupo duplicado',
                'slug' => 'grupo-reusable',
                'description' => 'Duplicado',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
            $this->fail('Duplicate active group slug should violate unique partial index');
        } catch (QueryException $e) {
            $this->addToAssertionCount(1);
        }

        DB::table('pages')->insert([
            'title' => 'Página borrada',
            'slug' => 'pagina-reusable',
            'excerpt' => 'Resumen',
            'content' => 'Contenido',
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => now(),
        ]);

        DB::table('pages')->insert([
            'title' => 'Página activa',
            'slug' => 'pagina-reusable',
            'excerpt' => 'Resumen',
            'content' => 'Contenido',
            'status' => 'published',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        try {
            DB::table('pages')->insert([
                'title' => 'Página duplicada',
                'slug' => 'pagina-reusable',
                'excerpt' => 'Resumen',
                'content' => 'Contenido',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
            $this->fail('Duplicate active page slug should violate unique partial index');
        } catch (QueryException $e) {
            $this->addToAssertionCount(1);
        }
    }

    public function test_filament_unique_validation_ignores_soft_deleted_records(): void
    {
        $admin = User::factory()->create([
            'role_id' => 1,
            'nick' => 'admin_idx',
        ]);

        $trashed = Category::create([
            'title' => 'Categoria Papelera',
            'slug' => 'papelera-slug',
            'description' => 'En papelera',
        ]);
        $trashed->delete();

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(CreateCategory::class)
            ->fillForm([
                'title' => 'Categoria Activa Nueva',
                'slug' => 'papelera-slug',
                'description' => 'Activa',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'title' => 'Categoria Activa Nueva',
            'slug' => 'papelera-slug',
            'deleted_at' => null,
        ]);
    }

    public function test_additive_migrations_can_roll_back_and_reapply_cleanly(): void
    {
        $exitRollback = Artisan::call('migrate:rollback', ['--step' => 2]);
        $this->assertSame(0, $exitRollback);

        $this->assertNotContains('suggestions_type_id_index', Schema::getIndexListing('suggestions'));
        $this->assertNotContains('categories_slug_unique_active', Schema::getIndexListing('categories'));
        $this->assertContains('categories_slug_unique', Schema::getIndexListing('categories'));

        $exitMigrate = Artisan::call('migrate');
        $this->assertSame(0, $exitMigrate);

        $this->assertContains('suggestions_type_id_index', Schema::getIndexListing('suggestions'));
        $this->assertContains('categories_slug_unique_active', Schema::getIndexListing('categories'));
    }
}
