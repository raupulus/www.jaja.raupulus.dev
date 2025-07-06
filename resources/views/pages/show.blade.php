@extends('layouts.master')

@section('title', $page->title)
@section('description', $page->excerpt)
@section('keywords', $page->keywords)

@if ($page->urlImage)
    @section('og_image', $page->urlImage)
    @section('twitter_image', $page->urlImage)
@endif

@section('css')
    @vite(['resources/css/page.css', 'resources/css/social_icons.css'])

    @if($page->slug === 'agradecimientos')
        @vite(['resources/css/contributors.css', 'resources/css/collaborators.css', 'resources/css/general_stats.css'])
    @endif
@endsection

@section('content')
    <div class="page-container">
        <div class="page-header">
            <h1 class="page-title">{{ $page->title }}</h1>

            @if ($page->urlImage)
                <img src="{{$page->urlImage}}" alt="{{$page->title}}" style="max-width: 600px;">
            @endif
        </div>

        @if($page->slug === 'agradecimientos')

            {{-- Reconocimiento a usuarios que más han compartido --}}
            <section class="reconocimiento-section">
                <h2 class="reconocimiento-title">Reconocimiento Especial</h2>
                <p>Un aplauso especial para aquellos usuarios que han enviado más contenido y han ayudado a construir
                    esta biblioteca de humor:</p>

                <div class="usuarios-destacados">

                    @foreach(\App\Helpers\StatsHelper::getUsersMoreActive(20) as $us)
                        <div class="usuario-card">
                            <div class="app-icons-container">
                                <div class="app-icon chistes-icon">
                                    <span class="icon-value">{{$us['chistes']}}</span>
                                </div>
                                <div class="app-icon adivinanzas-icon">
                                    <span class="icon-value">{{$us['adivinanzas']}}</span>
                                </div>
                                <div class="app-icon quiz-icon">
                                    <span class="icon-value">{{$us['quiz']}}</span>
                                </div>
                            </div>
                            <div class="usuario-nick">{{$us['nick']}}</div>
                        </div>
                    @endforeach

                </div>
            </section>

            {{-- Reconocimiento a desarrolladores open source --}}
            @include('collaborators.partials._collaborators')


            @include('partials._general_stats')

        @endif

        @if($page->slug === 'api')

            <section class="page-content">
                <div class="page-content-wrapper">
                    <ul>
                        <li>
                            <a href="{{url('docs/collection.json')}}" download>
                                Descargar colección de Postman
                            </a>
                        </li>
                        <li>
                            <a href="{{url('docs/openapi.yaml')}}" download>
                                Descargar Especificación OpenAPI
                            </a>
                        </li>
                        <li>
                            <a href="{{url('docs')}}">
                                Documentación API
                            </a>
                        </li>
                        <li>
                            <a href="{{url('docs#autenticacion-POSTapi-v1-auth-login')}}">
                                Autenticación
                            </a>
                        </li>
                        <li>
                            <a href="{{url('docs#categorias-grupos-y-tipos-GETapi-v1-types')}}">
                                Categorías, Grupos y Tipos
                            </a>
                        </li>
                        <li>
                            <a href="{{url('docs#contenidos-GETapi-v1-random')}}">
                                Contenidos
                            </a>
                        </li>
                    </ul>
                </div>
            </section>
        @endif

        <section class="page-content">
            <div class="page-content-wrapper">
                {!! $page->getHtmlContent() !!}
            </div>
        </section>


    </div>

    {{-- Iconos de redes sociales --}}
    <section class="mt-3 mb-3">
        @include('partials._social_icons')
    </section>

@endsection
