<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['title' => 'General', 'slug' => 'general', 'description' => 'Temas diversos del mundo tecnológico'],
            ['title' => 'JavaScript', 'slug' => 'javascript', 'description' => 'Lenguaje popular para desarrollo web del lado del cliente'],
            ['title' => 'PHP', 'slug' => 'php', 'description' => 'Lenguaje ampliamente usado en el desarrollo web'],
            ['title' => 'Python', 'slug' => 'python', 'description' => 'Lenguaje versátil y de alto nivel usado en múltiples disciplinas'],
            ['title' => 'Java', 'slug' => 'java', 'description' => 'Lenguaje orientado a objetos común en grandes sistemas'],
            ['title' => 'CSS', 'slug' => 'css', 'description' => 'Herramienta clave para la presentación en la web'],
            ['title' => 'Base de Datos', 'slug' => 'base-de-datos', 'description' => 'Gestión y organización de la información digital'],
            ['title' => 'DevOps', 'slug' => 'devops', 'description' => 'Integración de desarrollo y operaciones de TI'],
            ['title' => 'Frontend', 'slug' => 'frontend', 'description' => 'Interfaz y experiencia del usuario en aplicaciones'],
            ['title' => 'Backend', 'slug' => 'backend', 'description' => 'Lógica de negocio y procesamiento en servidores'],
            ['title' => 'Bug Fixing', 'slug' => 'bug-fixing', 'description' => 'Resolución de errores en sistemas informáticos'],
            ['title' => 'Git', 'slug' => 'git', 'description' => 'Sistema de control de versiones para el desarrollo de software'],
            ['title' => 'Testing', 'slug' => 'testing', 'description' => 'Evaluación y verificación de software'],
            ['title' => 'Algoritmos', 'slug' => 'algoritmos', 'description' => 'Soluciones lógicas y estructuradas a problemas'],
            ['title' => 'Machine Learning', 'slug' => 'machine-learning', 'description' => 'Técnicas de aprendizaje automático basadas en datos'],
            ['title' => 'Seguridad', 'slug' => 'seguridad', 'description' => 'Protección de sistemas frente a amenazas digitales'],
            ['title' => 'UI/UX', 'slug' => 'ui-ux', 'description' => 'Diseño centrado en la interacción y experiencia del usuario'],
            ['title' => 'Código Legacy', 'slug' => 'codigo-legacy', 'description' => 'Sistemas heredados aún en uso'],
            ['title' => 'Scrum', 'slug' => 'scrum', 'description' => 'Marco de trabajo ágil para equipos de desarrollo'],
            ['title' => 'Linux', 'slug' => 'linux', 'description' => 'Sistema operativo basado en Unix de código abierto'],
            ['title' => 'Cloud Computing', 'slug' => 'cloud-computing', 'description' => 'Uso de recursos informáticos a través de internet'],
            ['title' => 'C', 'slug' => 'c', 'description' => 'Lenguaje de programación de bajo nivel y alto rendimiento'],
            ['title' => 'C++', 'slug' => 'c-plus-plus', 'description' => 'Lenguaje con soporte para programación orientada a objetos'],
            ['title' => 'C#', 'slug' => 'c-sharp', 'description' => 'Lenguaje moderno desarrollado por Microsoft'],
            ['title' => 'Go', 'slug' => 'go', 'description' => 'Lenguaje eficiente y concurrente desarrollado por Google'],
            ['title' => 'Rust', 'slug' => 'rust', 'description' => 'Lenguaje centrado en la seguridad y el rendimiento'],
            ['title' => 'TypeScript', 'slug' => 'typescript', 'description' => 'Superset tipado de JavaScript'],
            ['title' => 'HTML', 'slug' => 'html', 'description' => 'Lenguaje de marcado para estructurar contenido web'],
            ['title' => 'MongoDB', 'slug' => 'mongodb', 'description' => 'Base de datos orientada a documentos'],
            ['title' => 'Docker', 'slug' => 'docker', 'description' => 'Contenedores para aplicaciones y servicios'],
            ['title' => 'Kubernetes', 'slug' => 'kubernetes', 'description' => 'Orquestador de contenedores'],
            ['title' => 'CI/CD', 'slug' => 'ci-cd', 'description' => 'Automatización del ciclo de vida del software'],
            ['title' => 'Kanban', 'slug' => 'kanban', 'description' => 'Sistema visual para gestión de tareas'],
            ['title' => 'Shell', 'slug' => 'shell', 'description' => 'Lenguaje de scripts para sistemas Unix'],
            ['title' => 'Android', 'slug' => 'android', 'description' => 'Sistema operativo móvil basado en Linux'],
            ['title' => 'iOS', 'slug' => 'ios', 'description' => 'Sistema operativo de Apple para dispositivos móviles'],
            ['title' => 'Big Data', 'slug' => 'big-data', 'description' => 'Análisis y procesamiento de grandes volúmenes de datos'],
            ['title' => 'Blockchain', 'slug' => 'blockchain', 'description' => 'Tecnología distribuida para almacenamiento seguro'],
            ['title' => 'API', 'slug' => 'api', 'description' => 'Interfaz para la comunicación entre sistemas'],
            ['title' => 'React', 'slug' => 'react', 'description' => 'Biblioteca para interfaces de usuario'],
            ['title' => 'Node.js', 'slug' => 'node-js', 'description' => 'Entorno de ejecución para JavaScript en el servidor'],
            ['title' => 'Django', 'slug' => 'django', 'description' => 'Framework web de alto nivel para Python'],
            ['title' => 'Laravel', 'slug' => 'laravel', 'description' => 'Framework PHP para desarrollo web moderno'],
            ['title' => 'Firebase', 'slug' => 'firebase', 'description' => 'Plataforma de desarrollo de aplicaciones de Google'],
            ['title' => 'VS Code', 'slug' => 'vs-code', 'description' => 'Editor de código fuente ampliamente utilizado'],
            ['title' => 'Compiladores', 'slug' => 'compiladores', 'description' => 'Traducción de código fuente a código máquina'],
            ['title' => 'CLI', 'slug' => 'cli', 'description' => 'Interfaz de línea de comandos para tareas técnicas'],
            ['title' => 'Game Dev', 'slug' => 'game-dev', 'description' => 'Creación y diseño de videojuegos'],
            ['title' => 'Open Source', 'slug' => 'open-source', 'description' => 'Software de código abierto y colaborativo'],
            ['title' => 'Perl', 'slug' => 'perl', 'description' => 'Lenguaje versátil utilizado para tareas de scripting'],
            ['title' => 'Ruby', 'slug' => 'ruby', 'description' => 'Lenguaje elegante y expresivo usado en desarrollo web'],
            ['title' => 'NoSQL', 'slug' => 'nosql', 'description' => 'Bases de datos no relacionales para grandes volúmenes de datos'],
            ['title' => 'PostgreSQL', 'slug' => 'postgresql', 'description' => 'Sistema de bases de datos relacional avanzado'],
            ['title' => 'MySQL', 'slug' => 'mysql', 'description' => 'Sistema de bases de datos popular de código abierto'],
            ['title' => 'Redis', 'slug' => 'redis', 'description' => 'Almacenamiento clave-valor en memoria'],
            ['title' => 'Angular', 'slug' => 'angular', 'description' => 'Framework de desarrollo frontend'],
            ['title' => 'Vue.js', 'slug' => 'vue-js', 'description' => 'Framework progresivo para la interfaz de usuario'],
            ['title' => 'Svelte', 'slug' => 'svelte', 'description' => 'Framework de compilación para interfaces web'],
            ['title' => 'Express', 'slug' => 'express', 'description' => 'Framework web minimalista para Node.js'],
            ['title' => 'Next.js', 'slug' => 'next-js', 'description' => 'Framework para aplicaciones web con React'],
            ['title' => 'NestJS', 'slug' => 'nestjs', 'description' => 'Framework para aplicaciones backend en Node.js'],
            ['title' => 'Flask', 'slug' => 'flask', 'description' => 'Microframework para aplicaciones web en Python'],
            ['title' => 'Spring', 'slug' => 'spring', 'description' => 'Framework empresarial para Java'],
            ['title' => 'Symfony', 'slug' => 'symfony', 'description' => 'Framework PHP de componentes reutilizables'],
            ['title' => 'ASP.NET', 'slug' => 'asp-net', 'description' => 'Framework para desarrollo web de Microsoft'],
            ['title' => 'Supabase', 'slug' => 'supabase', 'description' => 'Alternativa de código abierto a Firebase'],
            ['title' => 'IntelliJ', 'slug' => 'intellij', 'description' => 'Entorno de desarrollo para lenguajes JVM'],
            ['title' => 'Eclipse', 'slug' => 'eclipse', 'description' => 'IDE de código abierto para Java y otros'],
            ['title' => 'NetBeans', 'slug' => 'netbeans', 'description' => 'Entorno de desarrollo para Java'],
            ['title' => 'Interpretación', 'slug' => 'interpretacion', 'description' => 'Ejecución de código línea a línea'],
            ['title' => 'IDE', 'slug' => 'ide', 'description' => 'Entorno integrado para escribir y depurar código'],
            ['title' => 'API Testing', 'slug' => 'api-testing', 'description' => 'Evaluación de interfaces de programación de aplicaciones'],
            ['title' => 'QA', 'slug' => 'qa', 'description' => 'Aseguramiento de calidad en desarrollo de software'],
            ['title' => 'Data Science', 'slug' => 'data-science', 'description' => 'Análisis de datos mediante métodos científicos'],
            ['title' => 'Dev Tools', 'slug' => 'dev-tools', 'description' => 'Herramientas auxiliares para desarrolladores'],
            ['title' => 'Robótica', 'slug' => 'robotica', 'description' => 'Diseño y programación de máquinas autónomas'],
            ['title' => 'AR/VR', 'slug' => 'ar-vr', 'description' => 'Tecnologías de realidad aumentada y virtual'],
            ['title' => 'Tecnología antigua', 'slug' => 'tecnologia-antigua', 'description' => 'Historia y evolución de la tecnología'],
            ['title' => 'Compresión de datos', 'slug' => 'compresion-de-datos', 'description' => 'Reducción del tamaño de archivos'],
            ['title' => 'Redes', 'slug' => 'redes', 'description' => 'Interconexión de sistemas informáticos'],
            ['title' => 'Navegadores web', 'slug' => 'navegadores-web', 'description' => 'Programas para acceder a contenidos web'],
            ['title' => 'Crashes', 'slug' => 'crashes', 'description' => 'Fallos inesperados en sistemas o aplicaciones'],
            ['title' => 'Documentación', 'slug' => 'documentacion', 'description' => 'Guías y referencias para proyectos de software'],
            ['title' => 'Licencias de software', 'slug' => 'licencias-de-software', 'description' => 'Tipos de permisos de uso de programas'],
            ['title' => 'Patrones de diseño', 'slug' => 'patrones-de-diseno', 'description' => 'Soluciones recurrentes a problemas comunes de desarrollo'],
            ['title' => 'Arquitectura de software', 'slug' => 'arquitectura-de-software', 'description' => 'Estructura general de sistemas complejos'],
            ['title' => 'Monolito vs Microservicios', 'slug' => 'monolito-vs-microservicios', 'description' => 'Modelos de diseño de aplicaciones'],
            ['title' => 'Containers', 'slug' => 'containers', 'description' => 'Entornos aislados para aplicaciones'],
            ['title' => 'Virtualización', 'slug' => 'virtualizacion', 'description' => 'Simulación de entornos informáticos'],
            ['title' => 'Scripting', 'slug' => 'scripting', 'description' => 'Automatización de tareas repetitivas'],
            ['title' => 'Data Mining', 'slug' => 'data-mining', 'description' => 'Extracción de información útil a partir de datos'],
            ['title' => 'Lenguajes esotéricos', 'slug' => 'lenguajes-esotericos', 'description' => 'Lenguajes de programación experimentales o de broma'],
        ];

        foreach ($categories as $category) {
            if (Category::where('slug', $category['slug'])->exists()) {
                continue;
            }

            Category::create($category);
        }
    }
}
