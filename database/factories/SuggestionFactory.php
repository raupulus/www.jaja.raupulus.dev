<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Suggestion;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @extends Factory<Suggestion>
 */
class SuggestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_id' => Type::factory(),
            'group_id' => Group::factory(),
            'nick' => Str::substr(Str::slug(fake()->userName(), '_'), 0, 30),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraph(),
            'image_path' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'is_adult' => false,
            'is_ai' => false,
            'approved_at' => null,
        ];
    }

    /**
     * Asegura la categoría General ID 1 antes de persistir el modelo y disparar Suggestion::boot().
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        CategoryFactory::ensureGeneralCategoryExists();

        return parent::create($attributes, $parent);
    }

    /**
     * Marca la sugerencia como ya aprobada.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved_at' => now(),
        ]);
    }
}
