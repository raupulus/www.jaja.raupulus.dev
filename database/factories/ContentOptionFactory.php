<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\ContentOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContentOption>
 */
class ContentOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content_id' => Content::factory(),
            'value' => fake()->sentence(3),
            'is_correct' => false,
            'image_path' => null,
            'order' => 1,
        ];
    }

    /**
     * Marca la opción como respuesta correcta.
     */
    public function correct(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => true,
        ]);
    }
}
