<?php

namespace Database\Factories;

use App\Models\Suggestion;
use App\Models\SuggestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SuggestionOption>
 */
class SuggestionOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'suggestion_id' => Suggestion::factory(),
            'value' => fake()->sentence(3),
            'is_correct' => false,
            'image_path' => null,
            'order' => 1,
        ];
    }

    /**
     * Marca la opción sugerida como correcta.
     */
    public function correct(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => true,
        ]);
    }
}
