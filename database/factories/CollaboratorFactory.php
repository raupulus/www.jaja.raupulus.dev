<?php

namespace Database\Factories;

use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Collaborator>
 */
class CollaboratorFactory extends Factory
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
            'name' => fake()->name(),
            'nick' => Str::substr(Str::slug(fake()->unique()->userName(), '_'), 0, 30),
            'website' => fake()->url(),
            'url_repositories' => fake()->url(),
            'image' => null,
            'description' => fake()->sentence(),
        ];
    }
}
