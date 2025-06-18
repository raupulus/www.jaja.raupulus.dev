# jaja.raupulus.dev

Proyecto Laravel con backend Filament y api para gestionar chistes y adivinanzas que consumir externamente mediante bots.

## Iniciar proyecto en desarrollo

### Preparar .env

Copiamos el .env y rellenamos los datos del entorno, nuestra base de datos y configuraciones necesarias.

```bash
cp .env.example .env
```

### Instalar dependencias

```bash
composer install
```

### Generar clave de aplicación

```bash
php artisan key:generate
```

### Crear enlace simbólico para el storage

```bash
php artisan storage:link
```

### Ejecutar migraciones para generar la base de datos

```bash
php artisan migrate
```

### Crear tantos usuarios como necesitemos

```bash
php artisan make:filament-user
```

Actualmente, no vemos interesante un sistema de roles más fuerte y por defecto todos los usuarios se crean sin acceso
al *panel* de administración.

Para garantizar el acceso (declarar un usuario como administrador) deberás actualizar manualmente el role de ese usuario
en la base de datos (role_id=1) que por defecto será 2.

Esto queda así para que en el futuro si escala el sistema de roles (nuevo role como editor, gestión de roles en el panel...)
podamos actualizar manteniendo la misma enumeración de roles, pero actualmente las necesidades de gestionar roles son 
muy básicas y no llega a ser interesante aumentar la complejidad y querys.

### Ejecutar seeders para añadir datos de ejemplo a la base de datos

```bash
php artisan db:seed
```

---

## Rutas web

Sitio Principal: /

Panel Administrador: /admin
Panel Usuarios: /panel

Login Administrador: /admin/login
Login Usuarios: /panel/login (También /login que redirige al anterior)


## Filament

Documentación: https://filamentphp.com/docs/

### Crear nuevo Recurso con formulario y tabla

```bash
php artisan make:filament-resource Customer --generate
```


## Api

Headers:

- Accept: application/json
- Content-type: application/json
- Añadir Autenticación Bearer token



## TODO prioritario

Listado de tareas pendientes a realizar antes de la primera publicación

- [x] Añadir favicon
- [x] Crear y Añadir logotipo en distintas versiones
- [x] Preparar metadatos SEO
- [x] Crear y añadir imágenes para previsualización de redes sociales
- [x] Rellenar información en secciones del home
- [x] Limitar subida desde el frontend a 2mb por imagen adjunta y bloquear otro tipo de contenido
- [x] Mejorar textos en metadatos para SEO dentro de layout "head.blade.php"
- [x] Crear página de normas al publicar/compartir y adjuntarlas en formulario de envío.
- [x] Crear página de política de privacidad
- [x] Crear página de política de cookies
- [x] Crear página de condiciones de uso indicando que el contenido subido se cede para ser usado por cualquiera
- [x] Crear página de agradecimiento/colaboradores tanto de github como de uploaders/users dónde se añada la cantidad de subidas que ha realizado y enlazarlo en el home
- [x] Crear Licencia para el proyecto (GPLv3?)
- [x] Añadir tipografía más estable/uniforme, actualmente se usa la del sistema "-apple-system"
- [x] Añadir enlaces a páginas creadas anteriormente en todos los apartados que se dejaron con enlaces "#"
- [x] Añadir a la página de agradecimientos (quizás también a la principal??) Resumen de estadísticas con cantidades de chistes subidos
- [x] Crear estadísticas para los usuarios que han subido más contenido y dinamizarlas
- [x] Limitar tamaño del nick en formulario a 25 mirar cuanto cuadra bien en las tarjetas para no permitir aquí 255 y romper diseños
- [x] Añadir previsualización de imagen al subir sugerencia de contenido
- [x] Crear Validation request para endpoint login api de usuario
- [x] Personalizar metatags en páginas (imágen si tuviera)
- [x] Crear .env para producción
- [x] Crear archivo de configuración para sitio virtual para apache
- [x] Cambiar acceso a panel de usuario desde "/user" a "/panel"
- [x] Mirar si usar el login en el panel de usuarios para no utilizar el de admin como ahora
- [x] Quitar widget de documentación en el panel de usuarios
- [x] Añadir estadísticas en el backend para contenidos y usuarios
- Añadir colaboradores de software a la db e intranet (tabla para colaboradores y otra con proyectos asociados a estos)
estos colaboradores podrán editar sus proyectos desde la intranet, también añadir nuevos pero sin cambiar estado "draft"
- Dinamizar colaboradores y proyectos para tener su propia página con listado de proyectos y ver cada proyecto individual,
esto permite tener más contenido indexado y dar visibilidad a los colaboradores. Habrá que rediseñar el bloque de
tarjetas para enlazar a ver colaborador con su listado de proyectos y cada proyecto a su página individual del mismo.
- Revisar migraciones y validaciones para el campo "nick" que no debe permitir más de 50 carácteres
- Crear comando laravel para regenerar las estadísticas cada 10m o 30m y añadirlo al cron
- Preparar generador de sitemap en cron de laravel
- Añadir recaptcha v3 en formulario de envío
- Generar documentación de api phpdoc con automatización
- Habilitar indexado en motores de búsqueda

## TODO con menor prioridad

- Revisar todos los iconos de las secciones en menú lateral y poner uno adecuado para cada CRUD
- Gestión de tokens en panel de usuarios
- Generar thumbnails de todas las imágenes a 120px, 300px y 600px?
- Revisar: Añadir gráficas en el backend con cantidad de peticiones api?
- Implementar gestión de preguntas tipo quiz con respuestas en el backend
- Frontend: Si seleccionan pregunta tipo quiz, debería mostrar 4 opciones (máximo) requeridas 2 (mínimo) para las respuestas
- Frontend: Crear efecto de jajas cayendo por el fondo de la web con distintas longitudes "ja" "jaja" "aj"... y opacidad
