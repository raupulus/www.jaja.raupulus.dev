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

### Ejecutar seeders para añadir datos de ejemplo a la base de datos

```bash
php artisan db:seed
```

---

## Rutas web

Sitio Principal: /

Panel: /admin

Login: /admin/login


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



## TODO

Listado de tareas pendientes a realizar

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
- Crear Licencia para el proyecto (GPLv3?)
- Añadir tipografía más estable/uniforme, actualmente se usa la del sistema "-apple-system"
- Añadir a la página de agradecimientos (quizás también a la principal??) Resumen de estadísticas con cantidades de chistes subidos
- Cambiar acceso a panel de usuario desde "/user" a "/panel"
- Añadir recaptcha v3 en formulario de envío
- Crear .env para producción
- Gestión de tokens en panel de usuarios
- Generar documentación de api phpdoc con automatización
- Preparar generador de sitemap en cron de laravel
- Habilitar indexado en motores de búsqueda
- Revisar: Añadir gráficas en el backend con cantidad de peticiones api?
- Implementar gestión de preguntas tipo quiz con respuestas en el backend
- Frontend: Crear efecto de jajas cayendo por el fondo de la web con distintas longituds "ja" "jaja" "aj"... y opacidad
- Frontend: Si seleccionan pregunta tipo quiz, debería mostrar 4 opciones requeridas para las respuestas
