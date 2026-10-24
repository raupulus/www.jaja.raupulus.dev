<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Content;
use App\Models\Group;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryEmptyContentTest extends TestCase
{
    use RefreshDatabase;

    private Type $type;
    private Group $group;
    private User $user;
    private Category $generalCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        // Crear primero la categoría General con ID 1 (requerida por Content::boot)
        $this->generalCategory = Category::create([
            'id' => 1,
            'title' => 'General',
            'slug' => 'general',
            'description' => 'Categoría general',
        ]);

        $this->user = User::factory()->create();

        $this->type = Type::create([
            'name' => 'Chistes',
            'slug' => 'chistes',
            'description' => 'Chistes variados',
        ]);

        $this->group = Group::create([
            'type_id' => $this->type->id,
            'title' => 'Chistes de Informática',
            'slug' => 'chistes-de-informatica',
            'description' => 'Risas para desarrolladores',
        ]);
    }

    public function test_category_without_content_returns_404_on_random_page(): void
    {
        $emptyCategory = Category::create([
            'title' => 'ASP.NET',
            'slug' => 'asp-net',
            'description' => 'Categoría de ASP.NET sin chistes',
        ]);

        $response = $this->get(route('content.categoria.content.random', $emptyCategory->slug));

        $response->assertStatus(404);
    }

    public function test_category_with_content_returns_200_on_random_page(): void
    {
        $category = Category::create([
            'title' => 'PHP',
            'slug' => 'php',
            'description' => 'Categoría de PHP',
        ]);

        $content = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'title' => 'Chiste de PHP',
            'content' => '¿Por qué PHP cruzo la calle?',
            'is_adult' => false,
        ]);

        $category->contents()->sync([$content->id]);

        $response = $this->get(route('content.categoria.content.random', $category->slug));

        $response->assertStatus(200);
        $response->assertSee('PHP');
    }

    public function test_category_without_content_is_hidden_from_categories_index(): void
    {
        $emptyCategory = Category::create([
            'title' => 'ASP.NET',
            'slug' => 'asp-net',
            'description' => 'Categoría vacía',
        ]);

        $categoryWithContent = Category::create([
            'title' => 'Python',
            'slug' => 'python',
            'description' => 'Categoría con contenido',
        ]);

        $content = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'title' => 'Chiste de Python',
            'content' => '¿Import antigravity?',
            'is_adult' => false,
        ]);

        $categoryWithContent->contents()->sync([$content->id]);

        $response = $this->get(route('content.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Python');
        $response->assertDontSee('ASP.NET');
    }

    public function test_group_without_content_returns_404_on_random_page(): void
    {
        $emptyGroup = Group::create([
            'type_id' => $this->type->id,
            'title' => 'Grupo Vacío',
            'slug' => 'grupo-vacio',
            'description' => 'Sin contenido',
        ]);

        $response = $this->get(route('content.group.content.random', $emptyGroup->slug));

        $response->assertStatus(404);
    }

    public function test_group_without_content_is_hidden_from_groups_index(): void
    {
        $emptyGroup = Group::create([
            'type_id' => $this->type->id,
            'title' => 'Grupo Sin Chistes',
            'slug' => 'grupo-sin-chistes',
            'description' => 'Sin contenido',
        ]);

        $content = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'title' => 'Chiste activo',
            'content' => 'Contenido activo',
            'is_adult' => false,
        ]);

        $response = $this->get(route('content.groups.index'));

        $response->assertStatus(200);
        $response->assertSee('Chistes de Informática');
        $response->assertDontSee('Grupo Sin Chistes');
    }

    public function test_category_with_only_adult_content_is_treated_as_empty_publicly(): void
    {
        $adultCategory = Category::create([
            'title' => 'Solo Adultos',
            'slug' => 'solo-adultos',
            'description' => 'Solo contenido adulto',
        ]);

        $content = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'title' => 'Chiste adulto',
            'content' => 'Contenido para adultos',
            'is_adult' => true,
        ]);

        $adultCategory->contents()->sync([$content->id]);

        $responseRandom = $this->get(route('content.categoria.content.random', $adultCategory->slug));
        $responseRandom->assertStatus(404);

        $responseIndex = $this->get(route('content.categories.index'));
        $responseIndex->assertDontSee('Solo Adultos');
    }

    public function test_scope_has_available_content_filters_empty_models(): void
    {
        $emptyCategory = Category::create([
            'title' => 'ASP.NET',
            'slug' => 'asp-net',
            'description' => 'Categoría vacía',
        ]);

        $activeCategory = Category::create([
            'title' => 'Laravel',
            'slug' => 'laravel',
            'description' => 'Categoría con contenido',
        ]);

        $content = Content::create([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'title' => 'Chiste Laravel',
            'content' => 'Artisan joke',
            'is_adult' => false,
        ]);

        $activeCategory->contents()->sync([$content->id]);

        $categories = Category::hasAvailableContent()->pluck('slug')->toArray();
        $this->assertContains('laravel', $categories);
        $this->assertNotContains('asp-net', $categories);

        $emptyGroup = Group::create([
            'type_id' => $this->type->id,
            'title' => 'Grupo Vacío',
            'slug' => 'grupo-vacio',
            'description' => 'Sin contenido',
        ]);

        $groups = Group::hasAvailableContent()->pluck('slug')->toArray();
        $this->assertContains('chistes-de-informatica', $groups);
        $this->assertNotContains('grupo-vacio', $groups);
    }
}
