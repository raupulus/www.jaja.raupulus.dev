@extends('layouts.master')

@section('title', $page->title)
@section('description', $page->excerpt)
@section('keywords', $page->keywords)

@if ($page->urlImage)
    @section('og_image', $page->urlImage)
    @section('twitter_image', $page->urlImage)
@endif

@section('css')
    @vite(['resources/css/pages.css'])

    @if($page->slug === 'agradecimientos')
        @vite(['resources/css/components.css', 'resources/css/general_stats.css'])
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
                <p>Un aplauso especial para aquellos usuarios que han enviado más contenido y han ayudado a construir esta biblioteca de humor:</p>

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
            <section class="desarrolladores-section">
                <h2 class="desarrolladores-title">Desarrolladores Open Source</h2>
                <p class="desarrolladores-intro">
                    Estos colaboradores han contribuido con el desarrollo de esta biblioteca de humor.
                </p>

                <div class="desarrolladores-container">


                    <div class="desarrollador-card">
                        <div class="dev-info">
                            <div class="dev-avatar">
                                <img src="https://github.com/raupulus.png" alt="Avatar desarrollador Raúl Caro Pastorino (@raupulus)">
                            </div>
                            <h3 class="dev-name">Raúl Caro</h3>
                            <p class="dev-nick">raupulus</p>
                            <div class="dev-links">
                                <a href="https://raupulus.dev" title="Enlace al sitio web personal de Raúl Caro Pastorino (@raupulus)" class="dev-link" target="_blank">🌐 Website</a>
                                <a href="https://github.com/raupulus" title="Enlace al sitio web personal de Raúl Caro Pastorino (@raupulus)" class="dev-link" target="_blank">🔗 Perfil GitHub</a>
                            </div>
                        </div>

                        <div class="project-info">
                            <div class="platform-logo github-logo"></div>
                            {{--
                            <div class="platform-logo gitlab-logo"></div>
                            <div class="platform-logo bitbucket-logo"></div>
                            --}}
                            <h4 class="project-name">JAJA Project</h4>
                            <a href="https://github.com/raupulus/www.jaja.raupulus.dev" class="project-link" target="_blank">
                                Ver Repositorio
                            </a>
                        </div>
                    </div>


                </div>
            </section>

            @include('partials._general_stats')

        @endif

        <section class="page-content">
            <div class="page-content-wrapper">
                {!! $page->getHtmlContent() !!}
            </div>
        </section>
    </div>


@endsection
