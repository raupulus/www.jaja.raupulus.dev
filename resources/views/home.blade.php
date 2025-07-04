@extends('layouts.master')

@section('head')
    @vite(['resources/css/home.css', 'resources/css/general_stats.css', 'resources/css/cookies.css', 'resources/css/suggestions_form.css', 'resources/js/cookies.js', 'resources/js/suggestions_forms.js'])


    @if(config('google.recaptcha.site_key'))
        <script
            src="https://www.google.com/recaptcha/api.js?render={{config('google.recaptcha.site_key')}}&onload=onRecaptchaLoad"
            async defer></script>

        <script>

            function onRecaptchaLoad() {
                grecaptcha.ready(function () {
                    grecaptcha.execute("{{config('google.recaptcha.site_key')}}", {
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

@section('content')

    <section>
        <h1>Bienvenid@ a {{config('app.name')}}</h1>

        <div class="mw-800 m-auto">
            <img src="{{asset('images/portada-jaja-project.webp')}}" alt="Logo" class="logo">
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

    <section>
        <p class="mw-800 m-auto text-center text-secondary">
            Aquí puedes disfrutar del contenido más esporádico y subir tus propias invenciones.
        </p>

        <p class="mw-800 m-auto text-center text-secondary">
            Además tenemos una API para llevar el humor a tus bots, apps o proyectos.
        </p>

        <p class="mw-800 m-auto text-center text-destacado">
            ¿Contará tu <strong>bot</strong> mejores chistes que tú?
        </p>
    </section>

    <section class="card">
        <h2 class="card-title">Documentación API</h2>

        <p>
            Tenemos una api documentada para que puedas crear tus propios bots o aplicaciones. Puedes utilizar un
            endpoint sin ningún tipo de registro para obtener contenido aleatorio o solicitar unirte a nuestra comunidad
            para acceder a más contenido, más ratio de consultas y obtener contenido filtrado.
        </p>

        <a href="{{route('page.show', 'api')}}" title="Enlace a la documentación de la api en {{config('app.name')}}"
           class="btn">A happy Api Docs</a>
    </section>

    <section class="card">
        <h2 class="card-title">Bot para Twitch</h2>

        <p>
            WIP - En desarrollo. Estamos creando un bot para que puedas añadirlo a tu canal de Twitch y disfrutar del
            contenido directamente en el chat de tu canal utilizando el comando !chiste y !adivina con tu propio bot
            bajo tu control.
        </p>

        <a href="{{route('page.show', 'bot-twitch')}}" title="Enlace a la información sobre el bot de twitch"
           class="btn">Quiero mi BotHijo</a>
    </section>

    <section class="card">
        <h2 class="card-title">Buena Gente</h2>

        <p>
            La comunidad de jajajeros os agradece la colaboración y apoyo de todas esas personas que ayudan a que esto
            sea posible
        </p>

        <a href="{{route('page.show', 'agradecimientos')}}"
           title="Enlace a los agradecimientos de colaboradores y desarrolladores de {{config('app.name')}}"
           class="btn">JAgradece A...</a>
    </section>

    <section class="card">
        <h2 class="card-title">¡Envía el tuyo!</h2>

        <p>
            ¿Se te ha ocurrido algún chiste, pregunta tipo quiz o adivinanza?
        </p>

        <p>
            Comparte tu contenido con la comunidad para que todos lo disfrutemos pero asegúrate de que no sean
            inapropiados o incumplan <a href="{{route('page.show', 'normas')}}"
                                        title="Enlace a las normas de la comunidad en {{config('app.name')}}"
                                        target="_blank">las normas</a> .
        </p>

        {{-- Formulario  para enviar sugerencias --}}
        <div>
            @include('partials.forms._form_suggestions')
        </div>
    </section>

    <section class="card">
        <h2 class="card-title">Listado de Páginas</h2>

        <p>
            Aquí tienes un listado de todas las páginas del sitio de comunidad para que puedas navegar sin problemas.
            Puedes encontrar documentación legal, normas, información del sitio y contribuidores.
        </p>

        <a href="{{route('page.index')}}"
           title="Enlace a todo el listado de páginas de {{config('app.name')}}"
           class="btn">Legalisasion</a>
    </section>

    <section class="box-card-content">
        <h2>Algunos contenidos</h2>

        @foreach($contents as $content)
            <div class="card card-content">
                @if ($content->image)
                    <div class="card-content-image">
                        <img src="{{$content->urlImage}}" title="{{$content->title}}" alt="{{$content->title}}"/>
                    </div>
                @endif

                <div class="card-content-body">
                    <h3>{{$content->title}}</h3>

                    <p>
                        {!! $content->formattedHtmlContent !!}
                    </p>
                </div>

                <div class="card-content-meta">
                    <span>{{$content->group?->title}}</span>
                    <span>Por: {{$content->uploader}}</span>
                </div>

            </div>
        @endforeach
    </section>


    {{-- Cookies --}}
    @include('partials._cookies')
@endsection

@section('js')
    <script>
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                goToForm();

            });
        @endif
    </script>
@endsection

