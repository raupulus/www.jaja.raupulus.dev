<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Group;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $groups = Group::all();
        $debugCategory = Category::where('title', 'Bug Fixing')->first();
        $frontendCategory = Category::where('title', 'Frontend')->first();
        $databaseCategory = Category::where('title', 'Base de Datos')->first();
        $devopsCategory = Category::where('title', 'DevOps')->first();
        $generalCategory = Category::where('title', 'JavaScript')->first();

        $contents = [
            // Grupo 1: Chistes de Debugging
            [
                'group_id' => $groups[0]->id,
                'title' => '¿Por qué los programadores prefieren el debugging?',
                'content' => 'Porque es el único lugar donde sus errores se convierten en features.',
                'category_id' => $debugCategory->id
            ],
            [
                'group_id' => $groups[0]->id,
                'title' => 'Debug como detective',
                'content' => '¿Qué le dice un programador a otro? No te preocupes, no es un bug, es un detective feature.',
                'category_id' => $debugCategory->id
            ],
            [
                'group_id' => $groups[0]->id,
                'title' => 'El optimista programador',
                'content' => 'Un programador optimista dice: "El vaso está medio lleno". Un programador pesimista dice: "El vaso está medio vacío". Un programador debugger dice: "El vaso es el doble de grande de lo necesario".',
                'category_id' => $debugCategory->id
            ],
            [
                'group_id' => $groups[0]->id,
                'title' => 'Bug en producción',
                'content' => '¿Por qué los bugs aparecen en producción? Porque les gusta el ambiente live.',
                'category_id' => $debugCategory->id
            ],
            [
                'group_id' => $groups[0]->id,
                'title' => 'Debugger filosófico',
                'content' => 'Si un programa falla en producción y nadie está monitoreando los logs, ¿realmente falló?',
                'category_id' => $debugCategory->id
            ],

            // Grupo 2: Chistes de Programadores
            [
                'group_id' => $groups[1]->id,
                'title' => 'El programador en el supermercado',
                'content' => 'Mi esposa me mandó al supermercado y me dijo: "Compra una barra de pan, y si hay huevos, trae doce". Volví con 12 barras de pan. Ella: "¿Por qué compraste 12 barras?" Yo: "¡Porque había huevos!"',
                'category_id' => $generalCategory->id
            ],
            [
                'group_id' => $groups[1]->id,
                'title' => 'Arrays empiezan en 0',
                'content' => '¿Por qué los programadores confunden Halloween con Navidad? Porque Oct 31 = Dec 25',
                'category_id' => $generalCategory->id
            ],
            [
                'group_id' => $groups[1]->id,
                'title' => 'Programador vs Cafetera',
                'content' => '¿Qué tienen en común un programador y una cafetera? Los dos convierten recursos en código.',
                'category_id' => $generalCategory->id
            ],
            [
                'group_id' => $groups[1]->id,
                'title' => 'El código perfecto',
                'content' => '¿Cuántos programadores se necesitan para cambiar una bombilla? Ninguno, es un problema de hardware.',
                'category_id' => $generalCategory->id
            ],
            [
                'group_id' => $groups[1]->id,
                'title' => 'Recursividad',
                'content' => 'Para entender la recursividad, primero debes entender la recursividad.',
                'category_id' => $generalCategory->id
            ],

            // Grupo 3: Chistes de Base de Datos
            [
                'group_id' => $groups[2]->id,
                'title' => 'SQL Dating',
                'content' => '¿Por qué el programador SQL seguía soltero? Porque seguía tratando de mantener todas sus relaciones en NORMALIZED form.',
                'category_id' => $databaseCategory->id
            ],
            [
                'group_id' => $groups[2]->id,
                'title' => 'NoSQL vs SQL',
                'content' => '¿Por qué las bases de datos NoSQL son tan relajadas? Porque no tienen relaciones.',
                'category_id' => $databaseCategory->id
            ],
            [
                'group_id' => $groups[2]->id,
                'title' => 'JOIN perfecta',
                'content' => '¿Qué le dice una tabla a otra? Parece que tenemos una INNER CONNECTION.',
                'category_id' => $databaseCategory->id
            ],
            [
                'group_id' => $groups[2]->id,
                'title' => 'DROP TABLE',
                'content' => '¿Por qué el DBA está siempre estresado? Porque tiene miedo de DROP their responsibilities.',
                'category_id' => $databaseCategory->id
            ],
            [
                'group_id' => $groups[2]->id,
                'title' => 'Backup importante',
                'content' => '¿Cuál es el mantra de un DBA? "En BACKUP confío".',
                'category_id' => $databaseCategory->id
            ],

            // Grupo 4: Chistes de Frontend
            [
                'group_id' => $groups[3]->id,
                'title' => 'CSS Life',
                'content' => '¿Por qué el CSS está siempre triste? Porque tiene demasiados problemas de clase.',
                'category_id' => $frontendCategory->id
            ],
            [
                'group_id' => $groups[3]->id,
                'title' => 'JavaScript Promise',
                'content' => '¿Por qué JavaScript es tan malo haciendo promesas? Porque siempre las deja pending.',
                'category_id' => $frontendCategory->id
            ],
            [
                'group_id' => $groups[3]->id,
                'title' => 'Responsive Design',
                'content' => '¿Cómo llamas a un diseño web que no es responsive? Irresponsible design.',
                'category_id' => $frontendCategory->id
            ],
            [
                'group_id' => $groups[3]->id,
                'title' => 'HTML Dating',
                'content' => '¿Por qué el HTML y el CSS son una pareja perfecta? Porque el HTML proporciona la estructura y el CSS hace que todo se vea bonito.',
                'category_id' => $frontendCategory->id
            ],
            [
                'group_id' => $groups[3]->id,
                'title' => 'Frontend vs Backend',
                'content' => '¿Por qué el frontend y el backend no se llevan bien? Porque tienen problemas de comunicación en su API.',
                'category_id' => $frontendCategory->id
            ],

            // Grupo 5: Chistes de DevOps
            [
                'group_id' => $groups[4]->id,
                'title' => 'Docker Problems',
                'content' => '¿Por qué los contenedores de Docker son como los adolescentes? Porque nunca quieren compartir su espacio.',
                'category_id' => $devopsCategory->id
            ],
            [
                'group_id' => $groups[4]->id,
                'title' => 'Git Blame',
                'content' => '¿Por qué los desarrolladores aman git blame? Porque es la única manera socialmente aceptable de señalar culpables.',
                'category_id' => $devopsCategory->id
            ],
            [
                'group_id' => $groups[4]->id,
                'title' => 'Cloud Computing',
                'content' => '¿Por qué la nube está siempre triste? Porque está llena de servidores sin servidor.',
                'category_id' => $devopsCategory->id
            ],
            [
                'group_id' => $groups[4]->id,
                'title' => 'Kubernetes Life',
                'content' => '¿Qué le dice un pod a otro? Que orquesta-dos estamos.',
                'category_id' => $devopsCategory->id
            ],
            [
                'group_id' => $groups[4]->id,
                'title' => 'CI/CD Pipeline',
                'content' => '¿Por qué el pipeline de CI/CD está deprimido? Porque todos lo están haciendo fallar constantemente.',
                'category_id' => $devopsCategory->id
            ],
        ];

        foreach ($contents as $content) {

            if (Content::where('title', $content['title'])->exists()) {
                continue;
            }

            $categoryId = $content['category_id'];
            unset($content['category_id']);

            $contentModel = Content::create([
                ...$content,
                'user_id' => 1,
            ]);

            ContentCategory::create([
                'content_id' => $contentModel->id,
                'category_id' => $categoryId
            ]);
        }
    }
}
