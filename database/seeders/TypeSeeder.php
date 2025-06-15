<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Chistes', 'slug' => 'chistes', 'description' => 'Chistes, bromas y situaciones de humor'],
            ['name' => 'Quiz', 'slug' => 'quiz', 'description' => 'Preguntas tipo quiz'],
            ['name' => 'Adivinanzas', 'slug' => 'adivinanzas', 'description' => 'Adivinanzas para reflexionar'],
        ];

        foreach ($types as $type) {
            if (Type::where('slug', $type['slug'])->exists()) {
                continue;
            }

            Type::create($type);
        }
    }
}
