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
            ['title' => 'Chistes de Debugging', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes de Programadores', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes de Base de Datos', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes de Frontend', 'type_id' => $typeChistes->id],
            ['title' => 'Chistes de DevOps', 'type_id' => $typeChistes->id],
        ];

        foreach ($groups as $group) {
            Group::create($group);
        }
    }
}
