<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Chistes', 'description' => 'Chistes relacionados con tecnología y programación'],
            ['name' => 'Adivinanzas', 'description' => 'Adivinanzas sobre conceptos de programación'],
        ];

        foreach ($types as $type) {
            Type::create($type);
        }
    }
}
