<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['title' => 'JavaScript', 'slug' => 'javascript','description' => 'Chistes y adivinanzas sobre JavaScript'],
            ['title' => 'PHP', 'slug' => 'php', 'description' => 'Humor relacionado con PHP'],
            ['title' => 'Python', 'slug' => 'python', 'description' => 'Bromas sobre Python'],
            ['title' => 'Java', 'slug' => 'java', 'description' => 'Contenido humorístico sobre Java'],
            ['title' => 'CSS', 'slug' => 'css', 'description' => 'Chistes sobre diseño web y CSS'],
            ['title' => 'Base de Datos', 'slug' => 'db', 'description' => 'Humor sobre SQL y bases de datos'],
            ['title' => 'DevOps', 'slug' => 'devops', 'description' => 'Bromas sobre desarrollo y operaciones'],
            ['title' => 'Frontend', 'slug' => 'frontend', 'description' => 'Chistes sobre desarrollo frontend'],
            ['title' => 'Backend', 'slug' => 'backend', 'description' => 'Humor relacionado con backend'],
            ['title' => 'Bug Fixing', 'slug' => 'bug_fixing', 'description' => 'Bromas sobre depuración y errores'],
            ['title' => 'Git', 'slug' => 'git', 'description' => 'Chistes sobre control de versiones'],
            ['title' => 'Testing', 'slug' => 'testing', 'description' => 'Humor sobre pruebas de software'],
            ['title' => 'Algoritmos', 'slug' => 'algoritmos', 'description' => 'Bromas sobre algoritmos y lógica'],
            ['title' => 'Machine Learning', 'slug' => 'machine_learning', 'description' => 'Chistes sobre IA y ML'],
            ['title' => 'Seguridad', 'slug' => 'seguridad', 'description' => 'Humor sobre ciberseguridad'],
            ['title' => 'UI/UX', 'slug' => 'ui_ux', 'description' => 'Bromas sobre diseño de interfaces'],
            ['title' => 'Código Legacy', 'slug' => 'legacy_code', 'description' => 'Chistes sobre código heredado'],
            ['title' => 'Scrum', 'slug' => 'scrum', 'description' => 'Humor sobre metodologías ágiles'],
            ['title' => 'Linux', 'slug' => 'linux', 'description' => 'Bromas sobre sistemas operativos'],
            ['title' => 'Cloud Computing', 'slug' => 'cloud_computing', 'description' => 'Chistes sobre la nube'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
