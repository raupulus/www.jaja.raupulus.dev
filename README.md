# jaja.raupulus.dev

Proyecto WEB en Laravel con backend Filament y api sanctum para gestionar chistes, preguntas y adivinanzas que se pueden 
consumir externamente mediante bots, pwa o spa

Repositorios del proyecto:
- GitHub [https://github.com/raupulus/www.jaja.raupulus.dev](https://github.com/raupulus/www.jaja.raupulus.dev)
- GitLab [https://gitlab.com/raupulus/www.jaja.raupulus.dev](https://gitlab.com/raupulus/www.jaja.raupulus.dev)

## Presentación del Proyecto

[![Ver vídeo de presentación](https://img.youtube.com/vi/UnC0jZFXrak/hqdefault.jpg)](https://youtu.be/UnC0jZFXrak "Presentación del JaJa Project")

**[📺 Ver vídeo de presentación completo](https://youtu.be/UnC0jZFXrak)**

## Objetivos

El objetivo principal de este proyecto es tener una web y API de código abierto dónde pueda colaborar la comunidad
en su desarrollo y además tener un repositorio de contenido (chistes, preguntas, adivinanzas) comunitario en la web
[https://jaja.raupulus.dev](https://jaja.raupulus.dev) dónde cualquiera puede subir sugerencias de contenidos.

## Redes Sociales del proyecto

🤖 **Bot de Discord**: [Invitar a tu servidor](https://discord.com/oauth2/authorize?client_id=1391159444490158202&permissions=274877908992&integration_type=0&scope=bot)

🌐 **Síguenos en nuestras redes sociales**:
- **Bluesky**: [https://bsky.app/profile/jajupulus.bsky.social](https://bsky.app/profile/jajupulus.bsky.social)
- **Telegram**: [https://t.me/jajaproject](https://t.me/jajaproject)
- **Mastodon**: [https://mas.to/@jajupulus](https://mas.to/@jajupulus)
- **Twitter/X**: [https://x.com/jajupulus](https://x.com/jajupulus)

¡Únete a nuestra comunidad y mantente al día con las últimas actualizaciones del proyecto!

## API

Hemos desarrollado una api para que puedas utilizar los recursos aportados por la comunidad.

Puedes entrar a la documentación en [https://jaja.raupulus.dev/doc](https://jaja.raupulus.dev/doc).

Esta api dispone de un endpoint público sin necesidad de registro o cuenta de usuario que devuelve un contenido 
aleatorio de entre todos los disponibles.

También dispone de más endpoints para poder filtrar por tipo de contenido, categorías y grupos de contenidos los
cuales si necesitan una cuenta de colaborador que solo se puede conseguir contactándonos.

---

## Desplegar Proyecto

Instalar dependencias para desarrollo:

```bash
composer install
```

Instalar dependencias para producción:

```bash
composer install --no-dev
```


### Generar clave de aplicación

```bash
php artisan key:generate
```

### Crear enlace simbólico para el storage

```bash
php artisan storage:link
```

### Crear base de datos

Ejemplo para postgresql

```bash
sudo -u postgres createdb -O web -T template1 jaja_raupulus
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
al *panel* de administración en */admin*.

Para garantizar el acceso (declarar un usuario como administrador) deberás actualizar manualmente el role de ese usuario
en la base de datos (role_id=1) que por defecto será 2.

Esto queda así para que en el futuro si escala el sistema de roles (nuevo role como editor, gestión de roles en el panel...)
podamos actualizar manteniendo la misma enumeración de roles, pero actualmente las necesidades son 
muy básicas y no llega a ser interesante aumentar la complejidad y querys.

### Ejecutar seeders para añadir datos de ejemplo a la base de datos

```bash
php artisan db:seed
```

--- 

## Modo Desarrollo

### Generar Documentación API

Ten en cuenta que tras modificar la documentación para los endpoints de la API necesitamos ejecutar el comando
para generar la nueva versión actualizada:

```bash
php artisan scribe:generate
```

Es interesante revisar que en nuestro **.env** para desarrollo tengamos en la variable "SCRIBE_AUTH_KEY" el token
de un usuario de pruebas para que se obtengan peticiones reales y aparezcan los ejemplos de peticiones correctas.

---

## Principales Rutas web

Sitio Principal: /

Documentación api: /docs

Panel Administrador: /admin

Login Administrador: /admin/login

Panel Usuarios: /panel

Login Usuarios: /panel/login (También /login que redirige al anterior)


## Filament

Documentación: https://filamentphp.com/docs/

### Crear nuevo Recurso con formulario y tabla

```bash
php artisan make:filament-resource Content --generate
```


## Api

Headers:

- Accept: application/json
- Content-type: application/json
- Añadir Autenticación Bearer token

Documentación:

- /docs

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
- [x] En la api, las respuestas de los contenidos/grupos/tipos/categorías no se adapta a plural/singular según la cantidad de los devueltos
- [x] Generar documentación de api phpdoc con automatización
- [x] Enlazar documentación api en la página de API
- [x] Implementar middleware limitando peticiones para todas las rutas api
- [x] Habilitar indexado en motores de búsqueda
- [x] Mirar para quitar indexado en motores de búsqueda las rutas que comiencen por /api
- [x] Implementar gestión de preguntas tipo quiz con respuestas en el frontend
- [x] Frontend: Si seleccionan pregunta tipo quiz, debería mostrar 4 opciones (máximo) requeridas 2 (mínimo) para las respuestas
- [x] Implementar y mostrar las opciones adicionales en contenido cuando sea de tipo quiz
- [x] Implementar y mostrar las opciones adicionales en sugerencias cuando sea de tipo quiz
- [x] Implementar que al aprobar sugerencias, también se pasen sus opciones en caso de ser de tipo quiz
- [x] Obtener dirección ip real cuando viene de un proxy/cdn (por la abstracción que hace cloudflare)
- [x] Ocultar Opciones para tipos que no son "quiz"
- [x] Añadir opciones para quiz en panel de usuarios, solo lo tengo en el de admin actualmente
- [x] Poner las categorías en sugerencias y contenidos opcionales, si no hay ninguna añadir internamente "General"
- [x] Tras aprobar sugerencia, debería desaparecer el botón de aprobar para no añadir duplicados
- [x] Prevenir sitemap error al generar en server por permisos. Contemplar rellenar datos en lugar de eliminar completamente.
- [x] Del endpoint/random descartar contenido del grupo de chistes para adultos
- [x] Al publicar en redes sociales, descartar contenido del grupo de chistes para adultos
- [x] Revisar categorías en backend -> editar sugerencias, debería tomar "General" por defecto y no toma ninguna ahora
- [x] En panel de admin > Escritorio, cambiar consultas para estadísticas de sugerencias y contar las eliminadas
- [x] Mejorar página principal y enlazar a redes sociales
- [x] Añadir endpoint para poder subir sugerencias de chistes
- [x] Mejor control de contenido poco apropiado para todos los públicos
- [x] Mejorar filtros de contenido en endpoints de rutas aleatorias
- [x] Implementar gestión de reportes
- [x] Añadir gestión de reportes en el backend
- [x] Añadir endpoint para reportar contenido
- [x] Crear listado de chistes/adivinanzas filtrando por grupos en el frontend
- [x] Crear listado de chistes/adivinanzas filtrando por tipos en el frontend
- [x] Crear listado de chistes/adivinanzas filtrando por categorías en el frontend
- [x] Añadir al sitemap generator las nuevas vistas para listar grupos/tipos/categorías
- [x] Plantear cacheado para listado de categorías/tipos/grupos al cargar las páginas, reduciendo consultas
- [x] Enlazar listado de tipos, categorías y grupos en el home con 3 tarjetas en una fila
- [x] Mejorar la descripción de todos los tipos
- [x] Añadir imágenes y descripciones a los grupos
- [x] Añadir imágenes y descripciones a los tipos
- [x] Crear enlace para obtener bot de discord en el home con un icono muy visual
- [x] Diseñar imágenes para todas las páginas
- [x] Revisar todas las páginas para asegurar que tienen metadatos y descripciones bien formadas
- [x] Añadir selector de grupos en formulario de subida
- [x] Crear listado y filtros de contenidos al entrar en un grupo o categoría, crear vista para solo mostrar 3 y botón para ir refrescando contenido
- [x] Añadir ruta "content.group.content.random" a sitemap
- [x] Añadir ruta "content.categoria.content.random" a sitemap
- [x] En estadísticas del backend, sugerencias total, pendientes y aprobadas no se calculan bien
- [x] Añadir botón en panel admin para publicar en el momento en todas las redes sociales un contenido concreto o una entrada manual
- [x] Mover a endpoint público obtener contenido filtrando tipo: chiste/adivinanza/quiz
- [x] Aumentar límites de peticiones en los endpoints de la api para usuarios autenticados
- [x] Añadir "options" al devolver contenido de tipo quiz
- [x] Implementar lista de exclusión de ips al validar límites de peticiones
- [x] Crear endpoint para recibir un nick de un usuario y devolver un contenido random de él
- Cuando se apruebe una sugerencia y el nick de usuario exista, se debe asociar al usuario para no tener uploaders duplicados. Mirar raupulus https://jaja.raupulus.dev/pagina/agradecimientos 
- Revisar lista de categorías, creo que hay demasiadas y resulta incómodo... reducir lista y ser más genérico al etiquetar

## TODO con menor prioridad

- [x] En estadísticas de aportaciones por usuarios, descartar eliminados del filtro (bloque con carita, quiz, brain suma eliminados)
- [x] El bloque de resumen en la home en pantallas medianas con zoom muestra la tercera tarjeta debajo a la izquierda, centrar
- [x] Añadir en el panel de admin botón para ejecutar comando artisan y refrescar tanto el caché como el sitemap.
- [x] Al forzar delete en contenidos, poner evento para eliminar la imagen del hdd
- [x] Al forzar delete en sugerencias, poner evento para eliminar la imagen del hdd
- [x] Añadir imágenes a todos los grupos
- [x] Añadir imágenes a todos los tipos
- [x] Hacer bot para discord
- Vigilar captcha, da la sensación que alguna vez falla la carga pero no estoy seguro si es timeout por tardar (caducado)
- Revisar consultas api para tener en SELECT solo valores que se devuelven en la respuestas

## TODO para vídeotutorial

- Gestión de tokens en panel de usuarios
- [x] Implementar ordenar en relaciones

## TODO Mejoras interesantes pero no afectan a funcionalidad

- Unificar subidas de imágenes a un método que pueda reutilizar en todos lados en lugar de duplicar tanto código ahí
- Al mostrar preguntas de tipo quiz, el formato de las posibles respuestas es una lista ordenada sin estilos...

## Plantear si a futuro realmente lo queremos tener

- Generar thumbnails de todas las imágenes a 120px, 300px y 600px?
- Importar/exportar contenidos en excel+csv
- Frontend: Crear efecto de jajas cayendo por el fondo de la web con distintas longitudes "ja" "jaja" "aj"... y opacidad
- Crear contador de visita a endpoint bloqueando contador por día u hora restringiendo ip+domain
- Crear página de mantenimiento para cuando se esté actualizando (para php artisan down)
- En las tarjetas (frontend) de contenido total, colaboradores y risas generadas... enlazar a secciones?
- Al editar adivinanzas desde el grupos->modal de contenido, no tengo la selección de preguntas correctas. Plantear viabilidad.
- Bajar timeout de envío para fomularios y tras enviar la primera vez enviar timeout para que no sorprenda a la gente cuando intenta enviar por segunda vez
- Reportar contenido desde la web
