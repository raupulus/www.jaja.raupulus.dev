<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Type;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $typeChistes = Type::where('slug', 'chistes')->first();
        $typeQuiz = Type::where('slug', 'quiz')->first();
        $typeAdivinanzas = Type::where('slug', 'adivinanzas')->first();

        $groups = [
            ['title' => 'Chistes en General', 'slug' => 'chistes-general', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes de Programación', 'slug' => 'chistes-devs', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes de Lepe', 'slug' => 'chistes-lepe', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes Picantes', 'slug' => 'chistes-picantes', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes Juegos de Palabras', 'slug' => 'chistes-wordgames', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes Malos', 'slug' => 'chistes-malos', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes Infantiles', 'slug' => 'chistes-infantiles', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes Animales', 'slug' => 'chistes-infantiles', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes Frikis', 'slug' => 'chistes-frikis', 'type_id' => $typeChistes->id],

            ['title' => 'Preguntas en General', 'slug' => 'quiz-general', 'type_id' => $typeQuiz->id],
            ['title' => 'Preguntas Frikis', 'slug' => 'quiz-frikis', 'type_id' => $typeQuiz->id],
            ['title' => 'Preguntas Programación', 'slug' => 'quiz-devs', 'type_id' => $typeQuiz->id],
            ['title' => 'Preguntas Películas', 'slug' => 'quiz-devs', 'type_id' => $typeQuiz->id],

            ['title' => 'Adivinanza en General', 'slug' => 'adivinanzas-general', 'type_id' => $typeAdivinanzas->id],
        ];

        foreach ($groups as $group) {
            Group::create($group);
        }
    }
}
