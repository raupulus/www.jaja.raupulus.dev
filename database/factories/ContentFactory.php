<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Content>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'group_id' => Group::factory(),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraph(),
            'image' => null,
            'uploaded_by' => null,
            'is_adult' => false,
            'is_ai' => false,
            'last_social_published' => null,
        ];
    }

    /**
     * Asegura la categoría General ID 1 antes de persistir el modelo y disparar Content::boot().
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        CategoryFactory::ensureGeneralCategoryExists();

        return parent::create($attributes, $parent);
    }

    /**
     * Marca el contenido como adulto.
     */
    public function adult(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_adult' => true,
        ]);
    }

    /**
     * Marca el contenido como generado con IA.
     */
    public function ai(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_ai' => true,
        ]);
    }

    /**
     * Marca el contenido como aportación anónima mediante alias.
     */
    public function anonymous(string $nick = 'anon_user'): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'uploaded_by' => $nick,
        ]);
    }
}
