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
@endsection

@section('content')
    <div class="page-container">
        <div class="page-header">
            <h1 class="page-title">{{ $page->title }}</h1>

            @if ($page->urlImage)
                <img src="{{$page->urlImage}}" alt="{{$page->title}}" style="max-width: 600px;">
            @endif
        </div>

        <section class="page-content">
            <div class="page-content-wrapper">
                {!! $page->getHtmlContent() !!}
            </div>
        </section>
    </div>

    @if($page->slug === 'agradecimientos')
        AGRADECIMIENTOS:



        <p>
            Estadísticas de Contribución

            Estos números se actualizan automáticamente y reflejan el increíble trabajo de nuestra comunidad:

            Contenido Total Enviado: [Actualizado dinámicamente]
            Colaboradores Activos: [Contador en tiempo real]
            Risas Generadas: ¡Imposible de contar! 😄

        </p>

        <p>
            Reconocimiento Especial

            Un aplauso especial para aquellos usuarios que han enviado más contenido y han ayudado a construir esta biblioteca de humor:

            [Esta sección se actualiza automáticamente con los usuarios más activos]

            <br />

            @nick: 100 chistes, 31 adivinanzas
            <br />
            @nick1: 44 chistes, 22 adivinanzas
        </p>

        <p>
            Contribuidores Open Source

            Aquí aparecerán todos los colaboradores que han enviado pull requests, reportado issues y sugerido mejoras en GitHub.
        </p>
    @endif
@endsection
