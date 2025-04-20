<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['title' => 'JavaScript', 'description' => 'Chistes y adivinanzas sobre JavaScript'],
            ['title' => 'PHP', 'description' => 'Humor relacionado con PHP'],
            ['title' => 'Python', 'description' => 'Bromas sobre Python'],
            ['title' => 'Java', 'description' => 'Contenido humorístico sobre Java'],
            ['title' => 'CSS', 'description' => 'Chistes sobre diseño web y CSS'],
            ['title' => 'Base de Datos', 'description' => 'Humor sobre SQL y bases de datos'],
            ['title' => 'DevOps', 'description' => 'Bromas sobre desarrollo y operaciones'],
            ['title' => 'Frontend', 'description' => 'Chistes sobre desarrollo frontend'],
            ['title' => 'Backend', 'description' => 'Humor relacionado con backend'],
            ['title' => 'Bug Fixing', 'description' => 'Bromas sobre depuración y errores'],
            ['title' => 'Git', 'description' => 'Chistes sobre control de versiones'],
            ['title' => 'Testing', 'description' => 'Humor sobre pruebas de software'],
            ['title' => 'Algoritmos', 'description' => 'Bromas sobre algoritmos y lógica'],
            ['title' => 'Machine Learning', 'description' => 'Chistes sobre IA y ML'],
            ['title' => 'Seguridad', 'description' => 'Humor sobre ciberseguridad'],
            ['title' => 'UI/UX', 'description' => 'Bromas sobre diseño de interfaces'],
            ['title' => 'Código Legacy', 'description' => 'Chistes sobre código heredado'],
            ['title' => 'Scrum', 'description' => 'Humor sobre metodologías ágiles'],
            ['title' => 'Linux', 'description' => 'Bromas sobre sistemas operativos'],
            ['title' => 'Cloud Computing', 'description' => 'Chistes sobre la nube'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
