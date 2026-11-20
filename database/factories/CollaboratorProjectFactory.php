<?php

namespace Database\Factories;

use App\Models\Collaborator;
use App\Models\CollaboratorProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CollaboratorProject>
 */
class CollaboratorProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collaborator_id' => Collaborator::factory(),
            'title' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(3),
            'url' => fake()->url(),
            'image' => null,
            'excerpt' => fake()->sentence(),
            'content' => fake()->paragraphs(2, true),
            'type' => 'web',
            'repository_type' => 'github',
            'keywords' => 'php,laravel',
            'status' => 'published',
        ];
    }

    /**
     * Marca el proyecto en estado borrador.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}
