<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(2, true),
            'slug' => fake()->unique()->slug(2),
            'description' => fake()->sentence(),
            'image' => null,
        ];
    }

    /**
     * Garantiza que la categoría base ID 1 (General) exista para el evento boot de Content y Suggestion.
     */
    public static function ensureGeneralCategoryExists(): Category
    {
        if (! DB::table('categories')->where('id', 1)->exists()) {
            $slug = DB::table('categories')->where('slug', 'general')->exists()
                ? 'general-default'
                : 'general';

            DB::table('categories')->insert([
                'id' => 1,
                'title' => 'General',
                'slug' => $slug,
                'description' => 'Categoría general',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);

            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT setval(pg_get_serial_sequence('categories', 'id'), COALESCE((SELECT MAX(id) FROM categories), 1), true)");
            }
        }

        return Category::findOrFail(1);
    }
}
