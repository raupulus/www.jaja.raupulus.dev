<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>JaJa Project API Documentación Oficial</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .php-example code { display: none; }
                    body .content .python-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost:8000";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.2.1.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.2.1.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;php&quot;,&quot;python&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
            <img src="images/logos/jaja-project-logo-square-small.webp" alt="logo" class="logo" style="padding-top: 10px;" width="100%"/>
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="php">php</button>
                                            <button type="button" class="lang-button" data-language-name="python">python</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Buscar">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduccion" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduccion">
                    <a href="#introduccion">Introducción</a>
                </li>
                            </ul>
                    <ul id="tocify-header-autenticando-peticiones" class="tocify-header">
                <li class="tocify-item level-1" data-unique="autenticando-peticiones">
                    <a href="#autenticando-peticiones">Autenticando peticiones</a>
                </li>
                            </ul>
                    <ul id="tocify-header-autenticacion" class="tocify-header">
                <li class="tocify-item level-1" data-unique="autenticacion">
                    <a href="#autenticacion">🔐 Autenticación</a>
                </li>
                                    <ul id="tocify-subheader-autenticacion" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="autenticacion-POSTapi-v1-auth-login">
                                <a href="#autenticacion-POSTapi-v1-auth-login">Login</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacion-POSTapi-v1-auth-logout">
                                <a href="#autenticacion-POSTapi-v1-auth-logout">Logout</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacion-GETapi-v1-auth-user">
                                <a href="#autenticacion-GETapi-v1-auth-user">Información de usuario</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-categorias-grupos-y-tipos" class="tocify-header">
                <li class="tocify-item level-1" data-unique="categorias-grupos-y-tipos">
                    <a href="#categorias-grupos-y-tipos">🏷️ Categorías, Grupos y Tipos</a>
                </li>
                                    <ul id="tocify-subheader-categorias-grupos-y-tipos" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="categorias-grupos-y-tipos-GETapi-v1-types">
                                <a href="#categorias-grupos-y-tipos-GETapi-v1-types">Tipos de Contenido</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="categorias-grupos-y-tipos-GETapi-v1-groups">
                                <a href="#categorias-grupos-y-tipos-GETapi-v1-groups">Grupos de Contenidos</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="categorias-grupos-y-tipos-GETapi-v1-categories">
                                <a href="#categorias-grupos-y-tipos-GETapi-v1-categories">Categorías</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-contenidos" class="tocify-header">
                <li class="tocify-item level-1" data-unique="contenidos">
                    <a href="#contenidos">📚 Contenidos</a>
                </li>
                                    <ul id="tocify-subheader-contenidos" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="contenidos-GETapi-v1-random">
                                <a href="#contenidos-GETapi-v1-random">Contenido Aleatorio</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contenidos-GETapi-v1-type--type_slug--content-random">
                                <a href="#contenidos-GETapi-v1-type--type_slug--content-random">Contenido en base a un tipo</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contenidos-GETapi-v1-type--type_slug--category--categorySlug--content-random">
                                <a href="#contenidos-GETapi-v1-type--type_slug--category--categorySlug--content-random">Contenido en base a un tipo y Categoría</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contenidos-GETapi-v1-group--group_slug--content-random">
                                <a href="#contenidos-GETapi-v1-group--group_slug--content-random">Contenido en base a un grupo</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">Ver colección de Postman</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">Ver especificación OpenAPI</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Última actualización: June 26, 2025</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduccion">Introducción</h1>
<p>JaJa Project - La mejor comunidad para entretenimiento y compartir el humor</p>
<aside>
    <strong>URL Base</strong>: <code>http://localhost:8000</code>
</aside>
<pre><code>Esta documentación tiene como objetivo proporcionar toda la información necesaria para trabajar con nuestra API REST del proyecto JaJa. La API permite acceder a chistes, adivinanzas y contenido humorístico de manera programática, facilitando la integración con aplicaciones externas, bots de Discord, Telegram u otras plataformas que requieran contenido de entretenimiento.

Para comenzar a utilizar la API, necesitarás autenticarte mediante el endpoint de login con tus credenciales de usuario, lo que te proporcionará un token Bearer que deberás incluir en las cabeceras de autorización de tus peticiones posteriores. Una vez autenticado, podrás acceder a los diferentes endpoints para obtener contenido aleatorio, filtrar por tipos, categorías o grupos específicos, así como consultar la información de tu perfil de usuario. Todos los endpoints devuelven respuestas en formato JSON con una estructura consistente que incluye el estado de la operación, los datos solicitados y metadatos adicionales como información de paginación cuando corresponda.

&lt;aside&gt;Mientras navegas por esta documentación, verás ejemplos de código para trabajar con la API en diferentes lenguajes de programación en el área oscura de la derecha (o como parte del contenido en dispositivos móviles). Puedes cambiar el lenguaje utilizado con las pestañas en la parte superior derecha (o desde el menú de navegación en la parte superior izquierda en móviles). Los ejemplos incluyen las cabeceras HTTP necesarias, la estructura de las peticiones y las respuestas esperadas para cada endpoint.&lt;/aside&gt;</code></pre>

        <h1 id="autenticando-peticiones">Autenticando peticiones</h1>
<p>Para autenticar peticiones, incluye una cabecera <strong><code>Authorization</code></strong> con el valor <strong><code>"Bearer {TU_TOKEN_BEARER}"</code></strong>.</p>
<p>Todos los endpoints que requieren autenticación están marcados con una etiqueta <code>requiere autenticación</code> en la documentación a continuación.</p>
<p>Para obtener tu token logueate con tu cuenta de usuario y lo recibirás en la respuesta. También puedes acceder al panel para <b>generar el API TOKEN desde tu perfil</b>.</p>

        <h1 id="autenticacion">🔐 Autenticación</h1>

    

                                <h2 id="autenticacion-POSTapi-v1-auth-login">Login</h2>

<p>
</p>

<p>Lo usamos para obtener token de acceso api y de sesión (para SPA por ejemplo)</p>
<p>El Token Bearer devuelto lo usaremos en los headers para las peticiones que requieran autenticación:</p>
<p>Bearer 5|dpsZX6OKLdrx1wYDfJqyMjg3kdAGdrmzDU4gMkJ1be4af09b</p>

<span id="example-requests-POSTapi-v1-auth-login">
<blockquote>Ejemplo de petición:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/v1/auth/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"user@example.com\",
    \"password\": \"mipasswordsupersegura123123123\",
    \"device_name\": \"Mi iPhone 13 Pro\"
}"
</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/v1/auth/login';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'email' =&gt; 'user@example.com',
            'password' =&gt; 'mipasswordsupersegura123123123',
            'device_name' =&gt; 'Mi iPhone 13 Pro',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/v1/auth/login'
payload = {
    "email": "user@example.com",
    "password": "mipasswordsupersegura123123123",
    "device_name": "Mi iPhone 13 Pro"
}
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/auth/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "user@example.com",
    "password": "mipasswordsupersegura123123123",
    "device_name": "Mi iPhone 13 Pro"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-login">
            <blockquote>
            <p>Ejemplo de respuesta (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;token&quot;: &quot;5|dpsZX6OKLdrx1wYDfJqyMjg3kdAGdrmzDU4gMkJ1be4af09b&quot;,
        &quot;user&quot;: {
            &quot;name&quot;: &quot;Juan P&eacute;rez&quot;,
            &quot;nick&quot;: &quot;juanito&quot;,
            &quot;urlImage&quot;: &quot;https://ejemplo.com/storage/user-images/avatar.webp&quot;,
            &quot;email&quot;: &quot;juan@ejemplo.com&quot;,
            &quot;email_verified_at&quot;: &quot;2024-01-15T10:30:00.000000Z&quot;
        }
    },
    &quot;message&quot;: &quot;Login exitoso&quot;
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Las credenciales proporcionadas son incorrectas.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-auth-login" hidden>
    <blockquote>Respuesta recibida<span
                id="execution-response-status-POSTapi-v1-auth-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-login"
      data-empty-response-text="<Respuesta vacía>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-login" hidden>
    <blockquote>La petición falló con error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-login">

Consejo: Verifica que estés conectado correctamente a la red.
Si eres el mantenedor de esta API, verifica que tu API esté funcionando y que hayas habilitado CORS.
Puedes revisar la consola de Herramientas de Desarrollador para información de depuración.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-login" data-method="POST"
      data-path="api/v1/auth/login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-login', this);">
    <h3>
        Petición&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-login"
                    onclick="tryItOut('POSTapi-v1-auth-login');">Pruébalo ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-login"
                    onclick="cancelTryOut('POSTapi-v1-auth-login');" hidden>Cancelar 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-login"
                    data-initial-text="Enviar Petición 💥"
                    data-loading-text="⏱ Enviando..."
                    hidden>Enviar Petición 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Cabeceras</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Parámetros del cuerpo</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-v1-auth-login"
               value="user@example.com"
               data-component="body">
    <br>
<p>Email del usuario. El formato del value no es válido. Example: <code>user@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-v1-auth-login"
               value="mipasswordsupersegura123123123"
               data-component="body">
    <br>
<p>Contraseña del usuario. El campo value debe tener al menos 8 caracteres. El campo value debe ser menor que 100 caracteres. Example: <code>mipasswordsupersegura123123123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_name"                data-endpoint="POSTapi-v1-auth-login"
               value="Mi iPhone 13 Pro"
               data-component="body">
    <br>
<p>Nombre del dispositivo para generar el token y asociarlo a este. El campo value debe ser menor que 100 caracteres. Example: <code>Mi iPhone 13 Pro</code></p>
        </div>
        </form>

    <h3>Respuesta</h3>
    <h4 class="fancy-heading-panel"><b>Campos de respuesta</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>success</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Indica si la operación fue exitosa</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Mensaje descriptivo de la operación</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Datos de respuesta del login (solo si es exitoso)</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>token</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Token Bearer para autenticación en futuras peticiones</p>
                    </div>
                                                                <div style=" margin-left: 14px; clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>user</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Información del usuario autenticado</p>
            </summary>
                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Nombre completo del usuario</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>nick</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Apodo único del usuario</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>urlImage</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>URL completa de la imagen de perfil</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Email del usuario (incluido al ser el propio usuario)</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>email_verified_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Fecha de verificación del email ISO 8601</p>
                    </div>
                                    </details>
        </div>
                                        </details>
        </div>
                        <h2 id="autenticacion-POSTapi-v1-auth-logout">Logout</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Cierra la sesión de un usuario e invalida el token de acceso utilizado en ese momento</p>

<span id="example-requests-POSTapi-v1-auth-logout">
<blockquote>Ejemplo de petición:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/v1/auth/logout" \
    --header "Authorization: Bearer {TU_TOKEN_BEARER}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/v1/auth/logout';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_BEARER}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/v1/auth/logout'
headers = {
  'Authorization': 'Bearer {TU_TOKEN_BEARER}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers)
response.json()</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/auth/logout"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_BEARER}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-logout">
            <blockquote>
            <p>Ejemplo de respuesta (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: null,
    &quot;message&quot;: &quot;Sesi&oacute;n cerrada exitosamente&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-auth-logout" hidden>
    <blockquote>Respuesta recibida<span
                id="execution-response-status-POSTapi-v1-auth-logout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-logout"
      data-empty-response-text="<Respuesta vacía>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-logout" hidden>
    <blockquote>La petición falló con error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-logout">

Consejo: Verifica que estés conectado correctamente a la red.
Si eres el mantenedor de esta API, verifica que tu API esté funcionando y que hayas habilitado CORS.
Puedes revisar la consola de Herramientas de Desarrollador para información de depuración.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-logout" data-method="POST"
      data-path="api/v1/auth/logout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-logout', this);">
    <h3>
        Petición&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-logout"
                    onclick="tryItOut('POSTapi-v1-auth-logout');">Pruébalo ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-logout"
                    onclick="cancelTryOut('POSTapi-v1-auth-logout');" hidden>Cancelar 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-logout"
                    data-initial-text="Enviar Petición 💥"
                    data-loading-text="⏱ Enviando..."
                    hidden>Enviar Petición 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Cabeceras</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-auth-logout"
               value="Bearer {TU_TOKEN_BEARER}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_BEARER}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

    <h3>Respuesta</h3>
    <h4 class="fancy-heading-panel"><b>Campos de respuesta</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>success</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Indica si la operación fue exitosa</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Mensaje descriptivo de la operación</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
<br>
<p>null No se devuelven datos adicionales</p>
        </div>
                        <h2 id="autenticacion-GETapi-v1-auth-user">Información de usuario</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Devuelve la información del usuario actualmente logueado en la plataforma</p>

<span id="example-requests-GETapi-v1-auth-user">
<blockquote>Ejemplo de petición:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/v1/auth/user" \
    --header "Authorization: Bearer {TU_TOKEN_BEARER}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/v1/auth/user';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_BEARER}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/v1/auth/user'
headers = {
  'Authorization': 'Bearer {TU_TOKEN_BEARER}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/auth/user"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_BEARER}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-auth-user">
            <blockquote>
            <p>Ejemplo de respuesta (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;name&quot;: &quot;Juan P&eacute;rez&quot;,
        &quot;nick&quot;: &quot;juanito&quot;,
        &quot;urlImage&quot;: &quot;https://ejemplo.com/storage/user-images/avatar.webp&quot;,
        &quot;email&quot;: &quot;juan@ejemplo.com&quot;,
        &quot;email_verified_at&quot;: &quot;2024-01-15T10:30:00.000000Z&quot;
    },
    &quot;message&quot;: &quot;Informaci&oacute;n del usuario obtenida&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-auth-user" hidden>
    <blockquote>Respuesta recibida<span
                id="execution-response-status-GETapi-v1-auth-user"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-auth-user"
      data-empty-response-text="<Respuesta vacía>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-auth-user" hidden>
    <blockquote>La petición falló con error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-auth-user">

Consejo: Verifica que estés conectado correctamente a la red.
Si eres el mantenedor de esta API, verifica que tu API esté funcionando y que hayas habilitado CORS.
Puedes revisar la consola de Herramientas de Desarrollador para información de depuración.</code></pre>
</span>
<form id="form-GETapi-v1-auth-user" data-method="GET"
      data-path="api/v1/auth/user"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-auth-user', this);">
    <h3>
        Petición&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-auth-user"
                    onclick="tryItOut('GETapi-v1-auth-user');">Pruébalo ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-auth-user"
                    onclick="cancelTryOut('GETapi-v1-auth-user');" hidden>Cancelar 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-auth-user"
                    data-initial-text="Enviar Petición 💥"
                    data-loading-text="⏱ Enviando..."
                    hidden>Enviar Petición 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/auth/user</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Cabeceras</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-auth-user"
               value="Bearer {TU_TOKEN_BEARER}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_BEARER}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-auth-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-auth-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

    <h3>Respuesta</h3>
    <h4 class="fancy-heading-panel"><b>Campos de respuesta</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>success</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Indica si la operación fue exitosa</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Mensaje descriptivo de la operación</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Datos del usuario</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Nombre completo del usuario</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>nick</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Apodo único del usuario (máximo 25 caracteres)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>urlImage</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>URL completa de la imagen de perfil del usuario</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Email del usuario (solo si es el propio usuario autenticado)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>email_verified_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Fecha de verificación del email en formato ISO 8601 (solo si es el propio usuario autenticado)</p>
                    </div>
                                    </details>
        </div>
                    <h1 id="categorias-grupos-y-tipos">🏷️ Categorías, Grupos y Tipos</h1>

    

                                <h2 id="categorias-grupos-y-tipos-GETapi-v1-types">Tipos de Contenido</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Devuelve la lista de tipos de contenido que existen.</p>
<p>Útil para utilizar el slug del tipo que necesites y filtrar en otros endpoints.</p>

<span id="example-requests-GETapi-v1-types">
<blockquote>Ejemplo de petición:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/v1/types" \
    --header "Authorization: Bearer {TU_TOKEN_BEARER}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/v1/types';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_BEARER}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/v1/types'
headers = {
  'Authorization': 'Bearer {TU_TOKEN_BEARER}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/types"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_BEARER}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-types">
            <blockquote>
            <p>Ejemplo de respuesta (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;name&quot;: &quot;Quiz&quot;,
            &quot;slug&quot;: &quot;quiz&quot;,
            &quot;description&quot;: &quot;Preguntas relacionados con tecnolog&iacute;a y programaci&oacute;n&quot;,
            &quot;urlImage&quot;: null
        },
        {
            &quot;name&quot;: &quot;Chistes&quot;,
            &quot;slug&quot;: &quot;chistes&quot;,
            &quot;description&quot;: &quot;Chistes relacionados con tecnolog&iacute;a y programaci&oacute;n&quot;,
            &quot;urlImage&quot;: null
        },
        {
            &quot;name&quot;: &quot;Adivinanzas&quot;,
            &quot;slug&quot;: &quot;adivinanzas&quot;,
            &quot;description&quot;: &quot;Adivinanzas sobre conceptos de programaci&oacute;n&quot;,
            &quot;urlImage&quot;: null
        }
    ],
    &quot;message&quot;: &quot;Se obtuvieron 3 tipos&quot;,
    &quot;meta&quot;: {
        &quot;total_items&quot;: 3
    }
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;No se encontraron tipos de contenido&quot;
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Error al obtener la lista de tipos&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-types" hidden>
    <blockquote>Respuesta recibida<span
                id="execution-response-status-GETapi-v1-types"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-types"
      data-empty-response-text="<Respuesta vacía>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-types" hidden>
    <blockquote>La petición falló con error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-types">

Consejo: Verifica que estés conectado correctamente a la red.
Si eres el mantenedor de esta API, verifica que tu API esté funcionando y que hayas habilitado CORS.
Puedes revisar la consola de Herramientas de Desarrollador para información de depuración.</code></pre>
</span>
<form id="form-GETapi-v1-types" data-method="GET"
      data-path="api/v1/types"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-types', this);">
    <h3>
        Petición&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-types"
                    onclick="tryItOut('GETapi-v1-types');">Pruébalo ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-types"
                    onclick="cancelTryOut('GETapi-v1-types');" hidden>Cancelar 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-types"
                    data-initial-text="Enviar Petición 💥"
                    data-loading-text="⏱ Enviando..."
                    hidden>Enviar Petición 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/types</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Cabeceras</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-types"
               value="Bearer {TU_TOKEN_BEARER}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_BEARER}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-types"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-types"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

    <h3>Respuesta</h3>
    <h4 class="fancy-heading-panel"><b>Campos de respuesta</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>success</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Indica si la operación fue exitosa</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Mensaje descriptivo de la operación</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>Lista de tipos de contenido disponibles</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Nombre del tipo de contenido</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Slug del tipo para URLs amigables</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Descripción del tipo de contenido</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>urlImage</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
 &nbsp;
<br>
<p>URL completa de la imagen asociada al tipo (null si no tiene imagen)</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>meta</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Metadatos adicionales de la respuesta</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>total_items</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Número total de tipos disponibles</p>
                    </div>
                                    </details>
        </div>
                        <h2 id="categorias-grupos-y-tipos-GETapi-v1-groups">Grupos de Contenidos</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Devuelve la lista de grupos de contenido que existen paginados.</p>

<span id="example-requests-GETapi-v1-groups">
<blockquote>Ejemplo de petición:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/v1/groups?page=1&amp;limit=2" \
    --header "Authorization: Bearer {TU_TOKEN_BEARER}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/v1/groups';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_BEARER}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'page' =&gt; '1',
            'limit' =&gt; '2',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/v1/groups'
params = {
  'page': '1',
  'limit': '2',
}
headers = {
  'Authorization': 'Bearer {TU_TOKEN_BEARER}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, params=params)
response.json()</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/groups"
);

const params = {
    "page": "1",
    "limit": "2",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_BEARER}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-groups">
            <blockquote>
            <p>Ejemplo de respuesta (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;title&quot;: &quot;Preguntas en General&quot;,
            &quot;slug&quot;: &quot;quiz-general&quot;,
            &quot;urlImage&quot;: null
        },
        {
            &quot;title&quot;: &quot;Preguntas Programaci&oacute;n&quot;,
            &quot;slug&quot;: &quot;quiz-devs&quot;,
            &quot;urlImage&quot;: null
        }
    ],
    &quot;message&quot;: &quot;Se obtuvieron 2 grupos de contenido&quot;,
    &quot;meta&quot;: {
        &quot;pagination&quot;: {
            &quot;current_page&quot;: 1,
            &quot;per_page&quot;: 2,
            &quot;total&quot;: 17,
            &quot;last_page&quot;: 9,
            &quot;from&quot;: 1,
            &quot;to&quot;: 2,
            &quot;has_more_pages&quot;: true,
            &quot;has_previous_page&quot;: false,
            &quot;next_page_url&quot;: &quot;http://localhost:8000/api/v1/groups?groups_index=2&quot;,
            &quot;prev_page_url&quot;: null
        }
    }
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;La p&aacute;gina solicitada no existe. &Uacute;ltima p&aacute;gina disponible: 2&quot;
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;No se encontraron grupos de contenido&quot;
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Error al obtener la lista de grupos&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-groups" hidden>
    <blockquote>Respuesta recibida<span
                id="execution-response-status-GETapi-v1-groups"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-groups"
      data-empty-response-text="<Respuesta vacía>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-groups" hidden>
    <blockquote>La petición falló con error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-groups">

Consejo: Verifica que estés conectado correctamente a la red.
Si eres el mantenedor de esta API, verifica que tu API esté funcionando y que hayas habilitado CORS.
Puedes revisar la consola de Herramientas de Desarrollador para información de depuración.</code></pre>
</span>
<form id="form-GETapi-v1-groups" data-method="GET"
      data-path="api/v1/groups"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-groups', this);">
    <h3>
        Petición&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-groups"
                    onclick="tryItOut('GETapi-v1-groups');">Pruébalo ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-groups"
                    onclick="cancelTryOut('GETapi-v1-groups');" hidden>Cancelar 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-groups"
                    data-initial-text="Enviar Petición 💥"
                    data-loading-text="⏱ Enviando..."
                    hidden>Enviar Petición 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/groups</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Cabeceras</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-groups"
               value="Bearer {TU_TOKEN_BEARER}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_BEARER}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-groups"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-groups"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Parámetros de consulta</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-groups"
               value="1"
               data-component="query">
    <br>
<p>Número de página a obtener. El campo value debe tener al menos 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="limit"                data-endpoint="GETapi-v1-groups"
               value="2"
               data-component="query">
    <br>
<p>Cantidad de elementos por página (máximo 50). El campo value debe tener al menos 1. El campo value no debe de ser mayor a 50. Example: <code>2</code></p>
            </div>
                </form>

    <h3>Respuesta</h3>
    <h4 class="fancy-heading-panel"><b>Campos de respuesta</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>success</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Indica si la operación fue exitosa</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Mensaje descriptivo de la operación</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>Lista de grupos de contenido paginados</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Título del grupo</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Slug del grupo para URLs amigables</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>urlImage</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
 &nbsp;
<br>
<p>URL completa de la imagen asociada al grupo (null si no tiene imagen)</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>pagination</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Información de paginación</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>current_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Página actual</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>first_page_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>URL de la primera página</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>from</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Número del primer elemento en la página actual</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>last_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Número de la última página</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>last_page_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>URL de la última página</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>next_page_url</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
 &nbsp;
<br>
<p>URL de la siguiente página (null si es la última)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>path</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>URL base para la paginación</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Elementos por página</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>prev_page_url</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
 &nbsp;
<br>
<p>URL de la página anterior (null si es la primera)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Número del último elemento en la página actual</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>total</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Total de elementos disponibles</p>
                    </div>
                                    </details>
        </div>
                        <h2 id="categorias-grupos-y-tipos-GETapi-v1-categories">Categorías</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Devuelve la lista de categorías disponibles paginadas.</p>

<span id="example-requests-GETapi-v1-categories">
<blockquote>Ejemplo de petición:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/v1/categories?page=1&amp;limit=2" \
    --header "Authorization: Bearer {TU_TOKEN_BEARER}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/v1/categories';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_BEARER}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'page' =&gt; '1',
            'limit' =&gt; '2',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/v1/categories'
params = {
  'page': '1',
  'limit': '2',
}
headers = {
  'Authorization': 'Bearer {TU_TOKEN_BEARER}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, params=params)
response.json()</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/categories"
);

const params = {
    "page": "1",
    "limit": "2",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_BEARER}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-categories">
            <blockquote>
            <p>Ejemplo de respuesta (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;title&quot;: &quot;iOS&quot;,
            &quot;slug&quot;: &quot;ios&quot;,
            &quot;description&quot;: &quot;Sistema operativo de Apple para dispositivos m&oacute;viles&quot;,
            &quot;urlImage&quot;: null
        },
        {
            &quot;title&quot;: &quot;Vue.js&quot;,
            &quot;slug&quot;: &quot;vue-js&quot;,
            &quot;description&quot;: &quot;Framework progresivo para la interfaz de usuario&quot;,
            &quot;urlImage&quot;: null
        }
    ],
    &quot;message&quot;: &quot;Se obtuvieron 2 categor&iacute;as de 98, p&aacute;gina 1.&quot;,
    &quot;meta&quot;: {
        &quot;pagination&quot;: {
            &quot;current_page&quot;: 1,
            &quot;per_page&quot;: 2,
            &quot;total&quot;: 98,
            &quot;last_page&quot;: 49,
            &quot;from&quot;: 1,
            &quot;to&quot;: 2,
            &quot;has_more_pages&quot;: true,
            &quot;has_previous_page&quot;: false,
            &quot;next_page_url&quot;: &quot;http://localhost:8000/api/v1/categories?page=2&quot;,
            &quot;prev_page_url&quot;: null
        }
    }
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;No se han encontrado categor&iacute;as&quot;
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;La p&aacute;gina solicitada no existe. &Uacute;ltima p&aacute;gina disponible: 5&quot;
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Error al obtener la lista de categor&iacute;as&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-categories" hidden>
    <blockquote>Respuesta recibida<span
                id="execution-response-status-GETapi-v1-categories"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-categories"
      data-empty-response-text="<Respuesta vacía>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-categories" hidden>
    <blockquote>La petición falló con error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-categories">

Consejo: Verifica que estés conectado correctamente a la red.
Si eres el mantenedor de esta API, verifica que tu API esté funcionando y que hayas habilitado CORS.
Puedes revisar la consola de Herramientas de Desarrollador para información de depuración.</code></pre>
</span>
<form id="form-GETapi-v1-categories" data-method="GET"
      data-path="api/v1/categories"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-categories', this);">
    <h3>
        Petición&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-categories"
                    onclick="tryItOut('GETapi-v1-categories');">Pruébalo ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-categories"
                    onclick="cancelTryOut('GETapi-v1-categories');" hidden>Cancelar 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-categories"
                    data-initial-text="Enviar Petición 💥"
                    data-loading-text="⏱ Enviando..."
                    hidden>Enviar Petición 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/categories</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Cabeceras</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-categories"
               value="Bearer {TU_TOKEN_BEARER}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_BEARER}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Parámetros de consulta</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-categories"
               value="1"
               data-component="query">
    <br>
<p>Número de página a obtener. El campo value debe tener al menos 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="limit"                data-endpoint="GETapi-v1-categories"
               value="2"
               data-component="query">
    <br>
<p>Cantidad de elementos por página (máximo 50). El campo value debe tener al menos 1. El campo value no debe de ser mayor a 50. Example: <code>2</code></p>
            </div>
                </form>

    <h3>Respuesta</h3>
    <h4 class="fancy-heading-panel"><b>Campos de respuesta</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>success</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Indica si la operación fue exitosa</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Mensaje descriptivo de la operación</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>Lista de categorías paginadas</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Título de la categoría</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Slug de la categoría para URLs amigables</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Descripción de la categoría</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>urlImage</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
 &nbsp;
<br>
<p>URL completa de la imagen asociada a la categoría (null si no tiene imagen)</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>pagination</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Información de paginación</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>current_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Página actual</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>first_page_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>URL de la primera página</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>from</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Número del primer elemento en la página actual</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>last_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Número de la última página</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>last_page_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>URL de la última página</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>next_page_url</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
 &nbsp;
<br>
<p>URL de la siguiente página (null si es la última)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>path</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>URL base para la paginación</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Elementos por página</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>prev_page_url</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
 &nbsp;
<br>
<p>URL de la página anterior (null si es la primera)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Número del último elemento en la página actual</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>total</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Total de elementos disponibles</p>
                    </div>
                                    </details>
        </div>
                    <h1 id="contenidos">📚 Contenidos</h1>

    

                                <h2 id="contenidos-GETapi-v1-random">Contenido Aleatorio</h2>

<p>
</p>

<p>Devuelve un contenido aleatorio de entre todos los existentes en la plataforma sin filtro alguno.</p>
<p>Este endpoint al ser público está limitado a máximo 5 elementos por petición y a 1 petición por IP cada 5 segundos.</p>

<span id="example-requests-GETapi-v1-random">
<blockquote>Ejemplo de petición:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/v1/random?limit=1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/v1/random';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'limit' =&gt; '1',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/v1/random'
params = {
  'limit': '1',
}
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, params=params)
response.json()</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/random"
);

const params = {
    "limit": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-random">
            <blockquote>
            <p>Ejemplo de respuesta (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;title&quot;: &quot;Docker Problems&quot;,
            &quot;content&quot;: &quot;&iquest;Por qu&eacute; los contenedores de Docker son como los adolescentes? Porque nunca quieren compartir su espacio.&quot;,
            &quot;urlImage&quot;: null,
            &quot;uploader&quot;: &quot;@raupulus&quot;
        }
    ],
    &quot;message&quot;: &quot;Se obtuvieron 35 contenidos aleatorios&quot;,
    &quot;meta&quot;: {
        &quot;total_items&quot;: 35,
        &quot;limit&quot;: 1
    }
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;No se encontraron contenidos&quot;
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Error al obtener chistes aleatorios&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-random" hidden>
    <blockquote>Respuesta recibida<span
                id="execution-response-status-GETapi-v1-random"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-random"
      data-empty-response-text="<Respuesta vacía>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-random" hidden>
    <blockquote>La petición falló con error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-random">

Consejo: Verifica que estés conectado correctamente a la red.
Si eres el mantenedor de esta API, verifica que tu API esté funcionando y que hayas habilitado CORS.
Puedes revisar la consola de Herramientas de Desarrollador para información de depuración.</code></pre>
</span>
<form id="form-GETapi-v1-random" data-method="GET"
      data-path="api/v1/random"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-random', this);">
    <h3>
        Petición&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-random"
                    onclick="tryItOut('GETapi-v1-random');">Pruébalo ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-random"
                    onclick="cancelTryOut('GETapi-v1-random');" hidden>Cancelar 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-random"
                    data-initial-text="Enviar Petición 💥"
                    data-loading-text="⏱ Enviando..."
                    hidden>Enviar Petición 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/random</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Cabeceras</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-random"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-random"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Parámetros de consulta</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="limit"                data-endpoint="GETapi-v1-random"
               value="1"
               data-component="query">
    <br>
<p>Cantidad de elementos por página (máximo 5). El campo value debe tener al menos 1. El campo value no debe de ser mayor a 5. Example: <code>1</code></p>
            </div>
                </form>

    <h3>Respuesta</h3>
    <h4 class="fancy-heading-panel"><b>Campos de respuesta</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>success</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Indica si la operación fue exitosa</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Mensaje descriptivo de la operación</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>Colección de contenidos aleatorios</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Título del contenido (chiste, adivinanza, etc.)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>content</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Texto del contenido</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>urlImage</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
 &nbsp;
<br>
<p>URL completa de la imagen asociada al contenido (null si no tiene imagen)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>uploader</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Nombre del usuario que subió el contenido</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>meta</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Metadatos adicionales de la respuesta</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>total_items</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Número total de contenidos disponibles</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Límite aplicado en esta consulta</p>
                    </div>
                                    </details>
        </div>
                        <h2 id="contenidos-GETapi-v1-type--type_slug--content-random">Contenido en base a un tipo</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Devuelve un contenido aleatorio de un tipo concreto recibido.</p>

<span id="example-requests-GETapi-v1-type--type_slug--content-random">
<blockquote>Ejemplo de petición:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/v1/type/chistes/content/random?limit=1" \
    --header "Authorization: Bearer {TU_TOKEN_BEARER}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/v1/type/chistes/content/random';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_BEARER}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'limit' =&gt; '1',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/v1/type/chistes/content/random'
params = {
  'limit': '1',
}
headers = {
  'Authorization': 'Bearer {TU_TOKEN_BEARER}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, params=params)
response.json()</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/type/chistes/content/random"
);

const params = {
    "limit": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_BEARER}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-type--type_slug--content-random">
            <blockquote>
            <p>Ejemplo de respuesta (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;title&quot;: &quot;dfhg dfgh dfg h&quot;,
            &quot;content&quot;: &quot;df gh dfg hdfg h&quot;,
            &quot;urlImage&quot;: null,
            &quot;uploader&quot;: &quot;@test2&quot;
        }
    ],
    &quot;message&quot;: &quot;Se devuelve 1 contenido aleatorio para el tipo Chistes de 33 contenidos totales para este tipo.&quot;,
    &quot;meta&quot;: {
        &quot;type&quot;: &quot;Chistes&quot;,
        &quot;type_slug&quot;: &quot;chistes&quot;,
        &quot;total_items&quot;: 33
    }
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;No se encontraron contenidos para el tipo especificado&quot;
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Error al obtener contenidos del tipo especificado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-type--type_slug--content-random" hidden>
    <blockquote>Respuesta recibida<span
                id="execution-response-status-GETapi-v1-type--type_slug--content-random"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-type--type_slug--content-random"
      data-empty-response-text="<Respuesta vacía>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-type--type_slug--content-random" hidden>
    <blockquote>La petición falló con error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-type--type_slug--content-random">

Consejo: Verifica que estés conectado correctamente a la red.
Si eres el mantenedor de esta API, verifica que tu API esté funcionando y que hayas habilitado CORS.
Puedes revisar la consola de Herramientas de Desarrollador para información de depuración.</code></pre>
</span>
<form id="form-GETapi-v1-type--type_slug--content-random" data-method="GET"
      data-path="api/v1/type/{type_slug}/content/random"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-type--type_slug--content-random', this);">
    <h3>
        Petición&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-type--type_slug--content-random"
                    onclick="tryItOut('GETapi-v1-type--type_slug--content-random');">Pruébalo ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-type--type_slug--content-random"
                    onclick="cancelTryOut('GETapi-v1-type--type_slug--content-random');" hidden>Cancelar 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-type--type_slug--content-random"
                    data-initial-text="Enviar Petición 💥"
                    data-loading-text="⏱ Enviando..."
                    hidden>Enviar Petición 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/type/{type_slug}/content/random</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Cabeceras</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-type--type_slug--content-random"
               value="Bearer {TU_TOKEN_BEARER}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_BEARER}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-type--type_slug--content-random"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-type--type_slug--content-random"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Parámetros de URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>type_slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type_slug"                data-endpoint="GETapi-v1-type--type_slug--content-random"
               value="chistes"
               data-component="url">
    <br>
<p>El slug del tipo de contenido. Example: <code>chistes</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Parámetros de consulta</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="limit"                data-endpoint="GETapi-v1-type--type_slug--content-random"
               value="1"
               data-component="query">
    <br>
<p>Cantidad de elementos por página (máximo 5). El campo value debe tener al menos 1. El campo value no debe de ser mayor a 5. Example: <code>1</code></p>
            </div>
                </form>

    <h3>Respuesta</h3>
    <h4 class="fancy-heading-panel"><b>Campos de respuesta</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>success</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Indica si la operación fue exitosa</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Mensaje descriptivo de la operación</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>Lista con el contenido aleatorio solicitado</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Título del contenido</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>content</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Texto del contenido (chiste, adivinanza, etc.)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>urlImage</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
 &nbsp;
<br>
<p>URL completa de la imagen asociada al contenido (null si no tiene imagen)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>uploader</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Nick del usuario que subió el contenido (formato: @nick)</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>meta</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Metadatos adicionales de la respuesta</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Nombre del tipo de contenido</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>type_slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Slug del tipo</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>total_items</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Total de contenidos disponibles para este tipo</p>
                    </div>
                                    </details>
        </div>
                        <h2 id="contenidos-GETapi-v1-type--type_slug--category--categorySlug--content-random">Contenido en base a un tipo y Categoría</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Devuelve un contenido aleatorio que pertenezca al tipo y categoría recibido.</p>

<span id="example-requests-GETapi-v1-type--type_slug--category--categorySlug--content-random">
<blockquote>Ejemplo de petición:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/v1/type/chistes/category/javascript/content/random?limit=1" \
    --header "Authorization: Bearer {TU_TOKEN_BEARER}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/v1/type/chistes/category/javascript/content/random';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_BEARER}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'limit' =&gt; '1',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/v1/type/chistes/category/javascript/content/random'
params = {
  'limit': '1',
}
headers = {
  'Authorization': 'Bearer {TU_TOKEN_BEARER}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, params=params)
response.json()</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/type/chistes/category/javascript/content/random"
);

const params = {
    "limit": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_BEARER}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-type--type_slug--category--categorySlug--content-random">
            <blockquote>
            <p>Ejemplo de respuesta (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;title&quot;: &quot;JOIN perfecta&quot;,
            &quot;content&quot;: &quot;&iquest;Qu&eacute; le dice una tabla a otra? Parece que tenemos una INNER CONNECTION.&quot;,
            &quot;urlImage&quot;: null,
            &quot;uploader&quot;: &quot;@raupulus&quot;
        }
    ],
    &quot;message&quot;: &quot;Se devuelve 1 contenido aleatorio para el tipo Chistes y la categor&iacute;a JavaScript de 7 contenidos totales.&quot;,
    &quot;meta&quot;: {
        &quot;type&quot;: &quot;Chistes&quot;,
        &quot;type_slug&quot;: &quot;chistes&quot;,
        &quot;category&quot;: &quot;JavaScript&quot;,
        &quot;category_slug&quot;: &quot;javascript&quot;,
        &quot;total_items&quot;: 7
    }
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Categor&iacute;a no encontrada&quot;
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;No se encontraron contenidos para el tipo y categor&iacute;a especificados&quot;
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Error al obtener contenidos del tipo especificado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-type--type_slug--category--categorySlug--content-random" hidden>
    <blockquote>Respuesta recibida<span
                id="execution-response-status-GETapi-v1-type--type_slug--category--categorySlug--content-random"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-type--type_slug--category--categorySlug--content-random"
      data-empty-response-text="<Respuesta vacía>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-type--type_slug--category--categorySlug--content-random" hidden>
    <blockquote>La petición falló con error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-type--type_slug--category--categorySlug--content-random">

Consejo: Verifica que estés conectado correctamente a la red.
Si eres el mantenedor de esta API, verifica que tu API esté funcionando y que hayas habilitado CORS.
Puedes revisar la consola de Herramientas de Desarrollador para información de depuración.</code></pre>
</span>
<form id="form-GETapi-v1-type--type_slug--category--categorySlug--content-random" data-method="GET"
      data-path="api/v1/type/{type_slug}/category/{categorySlug}/content/random"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-type--type_slug--category--categorySlug--content-random', this);">
    <h3>
        Petición&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-type--type_slug--category--categorySlug--content-random"
                    onclick="tryItOut('GETapi-v1-type--type_slug--category--categorySlug--content-random');">Pruébalo ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-type--type_slug--category--categorySlug--content-random"
                    onclick="cancelTryOut('GETapi-v1-type--type_slug--category--categorySlug--content-random');" hidden>Cancelar 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-type--type_slug--category--categorySlug--content-random"
                    data-initial-text="Enviar Petición 💥"
                    data-loading-text="⏱ Enviando..."
                    hidden>Enviar Petición 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/type/{type_slug}/category/{categorySlug}/content/random</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Cabeceras</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-type--type_slug--category--categorySlug--content-random"
               value="Bearer {TU_TOKEN_BEARER}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_BEARER}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-type--type_slug--category--categorySlug--content-random"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-type--type_slug--category--categorySlug--content-random"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Parámetros de URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>type_slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type_slug"                data-endpoint="GETapi-v1-type--type_slug--category--categorySlug--content-random"
               value="chistes"
               data-component="url">
    <br>
<p>El slug del tipo de contenido. Example: <code>chistes</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>categorySlug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="categorySlug"                data-endpoint="GETapi-v1-type--type_slug--category--categorySlug--content-random"
               value="javascript"
               data-component="url">
    <br>
<p>El slug de la categoría. Example: <code>javascript</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Parámetros de consulta</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="limit"                data-endpoint="GETapi-v1-type--type_slug--category--categorySlug--content-random"
               value="1"
               data-component="query">
    <br>
<p>Cantidad de elementos por página (máximo 5). El campo value debe tener al menos 1. El campo value no debe de ser mayor a 5. Example: <code>1</code></p>
            </div>
                </form>

    <h3>Respuesta</h3>
    <h4 class="fancy-heading-panel"><b>Campos de respuesta</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>success</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Indica si la operación fue exitosa</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Mensaje descriptivo de la operación</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>Lista con el contenido aleatorio solicitado</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Título del contenido</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>content</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Texto del contenido (chiste, adivinanza, etc.)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>urlImage</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
 &nbsp;
<br>
<p>URL completa de la imagen asociada al contenido (null si no tiene imagen)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>uploader</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Nick del usuario que subió el contenido (formato: @nick)</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>meta</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Metadatos adicionales de la respuesta</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Nombre del tipo de contenido</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>type_slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Slug del tipo</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>category</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Título de la categoría</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>category_slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Slug de la categoría</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>total_items</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Total de contenidos disponibles para este tipo y categoría</p>
                    </div>
                                    </details>
        </div>
                        <h2 id="contenidos-GETapi-v1-group--group_slug--content-random">Contenido en base a un grupo</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Contenido aleatorio que pertenecen al grupo recibido.</p>

<span id="example-requests-GETapi-v1-group--group_slug--content-random">
<blockquote>Ejemplo de petición:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/v1/group/chistes-frontend/content/random?limit=1" \
    --header "Authorization: Bearer {TU_TOKEN_BEARER}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/v1/group/chistes-frontend/content/random';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_BEARER}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'limit' =&gt; '1',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/v1/group/chistes-frontend/content/random'
params = {
  'limit': '1',
}
headers = {
  'Authorization': 'Bearer {TU_TOKEN_BEARER}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, params=params)
response.json()</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/group/chistes-frontend/content/random"
);

const params = {
    "limit": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_BEARER}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-group--group_slug--content-random">
            <blockquote>
            <p>Ejemplo de respuesta (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;title&quot;: &quot;HTML Dating&quot;,
            &quot;content&quot;: &quot;&iquest;Por qu&eacute; el HTML y el CSS son una pareja perfecta? Porque el HTML proporciona la estructura y el CSS hace que todo se vea bonito.&quot;,
            &quot;urlImage&quot;: null,
            &quot;uploader&quot;: &quot;@raupulus&quot;
        }
    ],
    &quot;message&quot;: &quot;Se devuelve 1 contenido aleatorio para el grupo Chistes de Frontend de 5 contenidos totales.&quot;,
    &quot;meta&quot;: {
        &quot;group&quot;: &quot;Chistes de Frontend&quot;,
        &quot;group_slug&quot;: &quot;chistes-frontend&quot;,
        &quot;total_items&quot;: 5
    }
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;No se encontraron contenidos para el grupo especificado&quot;
}</code>
 </pre>
            <blockquote>
            <p>Ejemplo de respuesta (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Error al obtener contenidos del tipo especificado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-group--group_slug--content-random" hidden>
    <blockquote>Respuesta recibida<span
                id="execution-response-status-GETapi-v1-group--group_slug--content-random"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-group--group_slug--content-random"
      data-empty-response-text="<Respuesta vacía>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-group--group_slug--content-random" hidden>
    <blockquote>La petición falló con error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-group--group_slug--content-random">

Consejo: Verifica que estés conectado correctamente a la red.
Si eres el mantenedor de esta API, verifica que tu API esté funcionando y que hayas habilitado CORS.
Puedes revisar la consola de Herramientas de Desarrollador para información de depuración.</code></pre>
</span>
<form id="form-GETapi-v1-group--group_slug--content-random" data-method="GET"
      data-path="api/v1/group/{group_slug}/content/random"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-group--group_slug--content-random', this);">
    <h3>
        Petición&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-group--group_slug--content-random"
                    onclick="tryItOut('GETapi-v1-group--group_slug--content-random');">Pruébalo ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-group--group_slug--content-random"
                    onclick="cancelTryOut('GETapi-v1-group--group_slug--content-random');" hidden>Cancelar 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-group--group_slug--content-random"
                    data-initial-text="Enviar Petición 💥"
                    data-loading-text="⏱ Enviando..."
                    hidden>Enviar Petición 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/group/{group_slug}/content/random</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Cabeceras</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-group--group_slug--content-random"
               value="Bearer {TU_TOKEN_BEARER}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_BEARER}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-group--group_slug--content-random"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-group--group_slug--content-random"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Parámetros de URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>group_slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="group_slug"                data-endpoint="GETapi-v1-group--group_slug--content-random"
               value="chistes-frontend"
               data-component="url">
    <br>
<p>El slug del grupo de contenido. Example: <code>chistes-frontend</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Parámetros de consulta</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="limit"                data-endpoint="GETapi-v1-group--group_slug--content-random"
               value="1"
               data-component="query">
    <br>
<p>Cantidad de elementos por página (máximo 5). El campo value debe tener al menos 1. El campo value no debe de ser mayor a 5. Example: <code>1</code></p>
            </div>
                </form>

    <h3>Respuesta</h3>
    <h4 class="fancy-heading-panel"><b>Campos de respuesta</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>success</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Indica si la operación fue exitosa</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Mensaje descriptivo de la operación</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>Lista con el contenido aleatorio solicitado</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Título del contenido</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>content</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Texto del contenido (chiste, adivinanza, etc.)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>urlImage</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
 &nbsp;
<br>
<p>URL completa de la imagen asociada al contenido (null si no tiene imagen)</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>uploader</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Nick del usuario que subió el contenido (formato: @nick)</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>meta</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Metadatos adicionales de la respuesta</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>group</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Título del grupo</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>group_slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>Slug del grupo</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>total_items</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>Total de contenidos disponibles para este grupo</p>
                    </div>
                                    </details>
        </div>
                

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="php">php</button>
                                                        <button type="button" class="lang-button" data-language-name="python">python</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
