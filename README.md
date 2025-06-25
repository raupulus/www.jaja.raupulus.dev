# jaja.raupulus.dev

Proyecto Laravel con backend Filament y api para gestionar chistes y adivinanzas que consumir externamente mediante bots.

Repositorios del proyecto:
- GitHub [https://github.com/raupulus/www.jaja.raupulus.dev](https://github.com/raupulus/www.jaja.raupulus.dev)
- GitLab [https://gitlab.com/raupulus/www.jaja.raupulus.dev](https://gitlab.com/raupulus/www.jaja.raupulus.dev)

---


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

Login Administrador: /admin/login

Panel Usuarios: /panel

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
- [x] Añadir colaboradores de software a la db e intranet (tabla para colaboradores y otra con proyectos asociados a estos)
estos colaboradores podrán editar sus proyectos desde la intranet, también añadir nuevos pero sin cambiar estado "draft"
- [x] Dinamizar colaboradores y proyectos para tener su propia página con listado de proyectos y ver cada proyecto individual,
esto permite tener más contenido indexado y dar visibilidad a los colaboradores. Habrá que rediseñar el bloque de
tarjetas para enlazar a ver colaborador con su listado de proyectos y cada proyecto a su página individual del mismo.
- [x] Crear página con listado de páginas "/pages/index"
- [x] Revisar migraciones y validaciones para el campo "nick" que no debe permitir más de 50 carácteres
- [x] Crear comando laravel para regenerar las estadísticas cada 30m y añadirlo al cron
- [x] Crear comando laravel para generar sitemap.xml y añadirlo a cron de laravel
- [x] Añadir recaptcha v3 en formulario de envío
- [x] Revisar todos los iconos de las secciones en menú lateral del backend y poner uno adecuado para cada CRUD
- [x] Aceptación de cookies
- Generar documentación de api phpdoc con automatización
- Habilitar indexado en motores de búsqueda

## TODO con menor prioridad

- Gestión de tokens en panel de usuarios
- Generar thumbnails de todas las imágenes a 120px, 300px y 600px?
- Revisar: Añadir gráficas en el backend con cantidad de peticiones api?
- Implementar gestión de preguntas tipo quiz con respuestas en el backend
- Frontend: Si seleccionan pregunta tipo quiz, debería mostrar 4 opciones (máximo) requeridas 2 (mínimo) para las respuestas
- Frontend: Crear efecto de jajas cayendo por el fondo de la web con distintas longitudes "ja" "jaja" "aj"... y opacidad
- Unificar subidas de imágenes a un método que pueda reutilizar en todos lados en lugar de duplicar tanto código ahí
- Añadir en el panel de admin botón para ejecutar comando artisan y refrescar todo el caché. Así tras modificar
algo importante en el contenido puedo actualizar caché desde el propio panel.
- Importar/exportar contenidos en excel+csv
