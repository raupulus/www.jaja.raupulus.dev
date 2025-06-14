<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Chistes', 'slug' => 'chistes', 'description' => 'Chistes relacionados con tecnología y programación'],
            ['name' => 'Quiz', 'slug' => 'quiz', 'description' => 'Preguntas relacionados con tecnología y programación'],
            ['name' => 'Adivinanzas', 'slug' => 'adivinanzas', 'description' => 'Adivinanzas sobre conceptos de programación'],
        ];

        foreach ($types as $type) {
            Type::create($type);
        }
    }
}
