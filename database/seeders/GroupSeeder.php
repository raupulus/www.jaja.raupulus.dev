<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Type;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $typeChistes = Type::where('name', 'Chistes')->first();

        $groups = [
            ['title' => 'Chistes de Debugging', 'slug' => 'chistes-debugging', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes de Programadores', 'slug' => 'chistes-developers', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes de Base de Datos', 'slug' => 'chistes-db', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes de Frontend', 'slug' => 'chistes-frontend', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes de DevOps', 'slug' => 'chistes-devops', 'type_id' => $typeChistes->id],
        ];

        foreach ($groups as $group) {
            Group::create($group);
        }
    }
}
