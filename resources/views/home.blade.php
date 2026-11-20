@extends('layouts.master')

@section('title', config('app.name') . ' - Comunidad de Chistes, Adivinanzas y Preguntas Quiz')
@section('description', 'Descubre y comparte chistes, adivinanzas y preguntas tipo quiz en español. Comunidad abierta con API gratuita y bots para Discord y Twitch.')

@section('css')
    @vite(['resources/css/general_stats.css', 'resources/css/social_icons.css', 'resources/css/cookies.css', 'resources/css/suggestions_form.css', 'resources/css/groups_links.css'])
@endsection

@section('head')
    @if(config('google.recaptcha.site_key'))
        <script
            src="https://www.google.com/recaptcha/api.js?render={{ urlencode((string) config('google.recaptcha.site_key')) }}&onload=onRecaptchaLoad"
            async defer></script>

        <script>

            function onRecaptchaLoad() {
                grecaptcha.ready(function () {
                    grecaptcha.execute(@json(config('google.recaptcha.site_key')), {
                        action: 'suggestion_send'
                    }).then(function (token) {
                        //console.log('Token generado:', token);
                        if (token) {
                            document.getElementById('g_recaptcha').value = token;
                        }
                    }).catch(function (error) {
                        //console.error('Error generando token:', error);
                    });
                });
            }

        </script>
    @endif

@endsection

@push('jsonld')
    @include('partials._jsonld', [
        'schema' => [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => route('index') . '#website',
            'name' => config('app.name'),
            'url' => route('index'),
            'inLanguage' => 'es',
            'description' => 'Comunidad abierta en español para compartir chistes, adivinanzas y preguntas tipo quiz, con API gratuita y bots comunitarios.',
        ],
    ])
    @include('partials._jsonld', [
        'schema' => [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => route('index'),
            'logo' => asset('images/social/jaja-project-logo-social.jpg'),
            'sameAs' => [
                'https://t.me/jajaproject',
                'https://mas.to/@jajupulus',
                'https://bsky.app/profile/jajupulus.bsky.social',
                'https://x.com/jajupulus',
                'https://gitlab.com/raupulus/www.jaja.raupulus.dev',
                'https://github.com/raupulus/www.jaja.raupulus.dev',
            ],
        ],
    ])
@endpush

@section('content')

    <section>
        <h1>Bienvenid@ a {{config('app.name')}}</h1>

        <div class="mw-800 m-auto">
            <img src="{{asset('images/portada-jaja-project.webp')}}"
                 alt="Portada ilustrada de {{ config('app.name') }}, comunidad de chistes, adivinanzas y quiz"
                 class="logo"
                 width="800"
                 height="300"
                 fetchpriority="high"
                 decoding="async">
        </div>

        <p class="mw-800 m-auto text-center text-destacado">
            ¿List@ para hacer <strong>reír</strong> (o intentarlo jeje), compartir y crear en comunidad?
        </p>

        <p class="mw-800 m-auto text-center text-secondary">
            En <strong>JaJa Project</strong> venimos a tomarnos la risa en serio (bueno, más o menos)
            creando una comunidad abierta donde los <strong>chistes</strong>, adivinanzas y preguntas tipo quiz cobran
            vida con tus
            ocurrencias.
        </p>
    </section>

    {{-- Estadísticas generales del contenido en la plataforma --}}
    @include('partials._general_stats')

    <section class="mt-3 mb-3">
        <p class="mw-800 m-auto text-center text-secondary">
            Tenemos una
            <a href="{{route('page.show', 'api')}}">API</a>
            de comunidad para llevar el humor a tus bots, apps o proyectos.
        </p>
    </section>

    <hr>

    <section class="mt-3 mb-3">
        <p class="mw-800 m-auto text-center text-destacado">
            ¿Contará tu <strong>bot</strong> mejores chistes que tú?
        </p>
    </section>

    <section class="card">
        <h2 class="card-title">😜 ¡Envía el tuyo!</h2>

        <p>
            ¿Se te ha ocurrido algún chiste, pregunta tipo quiz o adivinanza?
        </p>

        <p>
            Comparte tu contenido con la comunidad para que todos lo disfrutemos pero asegúrate de que no sean
            inapropiados o incumplan <a href="{{route('page.show', 'normas')}}"
                                        title="Enlace a las normas de la comunidad en {{config('app.name')}}"
                                        target="_blank" rel="noopener noreferrer">las normas</a> .
        </p>

        {{-- Formulario  para enviar sugerencias --}}
        <div>
            @include('partials.forms._form_suggestions')
        </div>
    </section>

    {{-- Iconos de redes sociales --}}
    <section class="mt-3 mb-3">
        @include('partials._social_icons')
    </section>

    <section class="card">
        <h2 class="card-title">📚 Documentación API</h2>

        <p>
            Tenemos una api documentada para que puedas crear tus propios bots o aplicaciones. Puedes utilizar un
            endpoint sin ningún tipo de registro para obtener contenido aleatorio o solicitar unirte a nuestra comunidad
            para acceder a más contenido, más ratio de consultas y obtener contenido filtrado.
        </p>

        <a href="{{route('page.show', 'api')}}" title="Enlace a la documentación de la api en {{config('app.name')}}"
           class="btn">📚 A happy Api Docs</a>
    </section>

    <section class="card">
        <h2 class="card-title">🤖 Bots</h2>

        <p>
            Los colaboradores de la comunidad crean bots para que puedas disfrutar del contenido de la comunidad
            directamente en tu canal de Twitch, Discord, etc...

            Adopta desde hoy mismo el comando !chiste y !adivina en tu canal/servidor/chat
        </p>

        <a href="{{route('page.show', 'bots-para-la-api-de-jaja-project')}}"
           title="Enlace a la información sobre los bots de {{config('app.name')}}"
           class="btn">🤖 Quiero mi BotHijo</a>
    </section>

    {{-- Call to action - Añadir bot de discord a servidor --}}
    <div class="btn-dc-bot-container">
        @include('partials.buttons._dc_bot')
    </div>

    <section class="card">
        <h2 class="card-title">❤️ Buena Gente</h2>

        <p>
            La comunidad de jajajeros os agradece la colaboración y apoyo de todas esas personas que ayudan a que esto
            sea posible
        </p>

        <a href="{{route('page.show', 'agradecimientos')}}"
           title="Enlace a los agradecimientos de colaboradores y desarrolladores de {{config('app.name')}}"
           class="btn btn--outline">❤️ JAgradece A...</a>
    </section>


    <section class="card">
        <h2 class="card-title">🧑‍💻 Colaboradores</h2>

        <p>
            Estos son nuestros colaboradores secuestrados por la comunidad.
            Se encargan de mantener los servicios, la distribución de contenido, moderación, crear software...
        </p>

        <a href="{{route('collaborator.index')}}"
           title="Enlace a los colaboradores y sus proyectos de comunidad"
           class="btn btn--outline">🧑‍💻 Ver Colaboradores</a>
    </section>

    <section class="card">
        <h2 class="card-title">📄 Listado de Páginas</h2>

        <p>
            Aquí tienes un listado de todas las páginas del sitio de comunidad para que puedas tener toda la información
            en tu mano.
            Puedes encontrar documentación legal, normas, información del sitio, contribuidores...
        </p>

        <a href="{{route('page.index')}}"
           title="Enlace a todo el listado de páginas de {{config('app.name')}}"
           class="btn btn--outline">📄 Páginas para la comunidad</a>
    </section>

    {{-- Botones enlazando a listado de contenidos --}}
    @include('partials._groups_links')


    {{-- Listado de contenidos aleatorios --}}
    @include('contents.partials._contents', ['contents' => $contents, 'contents_title' => 'Algunos contenidos'])


    {{-- Call to action - Añadir bot de discord a servidor --}}
    <div class="btn-dc-bot-container">
        @include('partials.buttons._dc_bot')
    </div>

    {{-- Iconos de redes sociales --}}
    <section class="mt-3 mb-3">
        @include('partials._social_icons')
    </section>


    {{-- Cookies --}}
    @include('partials._cookies')
@endsection

@section('js')
    @vite(['resources/js/cookies.js', 'resources/js/suggestions_forms.js'])
    <script>
        @if($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            goToForm();
        });
        @endif
    </script>
@endsection

