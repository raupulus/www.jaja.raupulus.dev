@extends('layouts.master')

@section('title', 'Tipos de contenidos en ' . config('app.name'))
@section('description', 'Descubre todos los tipos de contenido disponibles en ' . config('app.name') . ': chistes, adivinanzas y preguntas tipo quiz creados por la comunidad.')
@section('keywords', implode(',', $types->pluck('name')->toArray()))

@section('css')
    @vite(['resources/css/social_icons.css', 'resources/css/horizontal_card.css'])
@endsection

@push('jsonld')
    @include('partials._jsonld', [
        'schema' => [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Tipos de contenido - ' . config('app.name'),
            'url' => route('content.types.index'),
            'inLanguage' => 'es',
        ],
    ])
    @include('partials._jsonld', [
        'schema' => [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Inicio',
                    'item' => route('index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Tipos',
                    'item' => route('content.types.index'),
                ],
            ],
        ],
    ])
@endpush

@section('content')
    @include('partials._breadcrumbs', [
        'items' => [
            ['label' => 'Inicio', 'url' => route('index')],
            ['label' => 'Tipos'],
        ],
    ])

    <h1 class="text-center">Tipos de contenido</h1>

    @forelse($types as $type)

        <article class="card-resume-horizontal">
            <div class="card-resume-left">
                <h2 class="card-resume-title">{{$type->name}}</h2>

                @if($type->image)
                    <div class="card-resume-image">
                        <img src="{{$type->urlImage}}" alt="{{$type->name}}" width="220" height="120" loading="lazy" decoding="async">
                    </div>
                @endif
            </div>

            <div class="card-resume-content">
                <p class="card-resume-description">
                    {{$type->description}}
                </p>
                <div class="card-resume-meta">
                    Última actualización: {{$type->last_update_message}}
                </div>
            </div>

            <div class="card-resume-right">
                <span class="card-resume-badge">{{$type->groups_count}}</span>
                <a href="{{route('content.type.group.index', $type->slug)}}" class="card-resume-button">Ver grupos de {{$type->name}}</a>
            </div>
        </article>

    @empty
        <div class="empty-state">
            <h2>Todavía no hay tipos de contenido disponibles</h2>
            <p>Estamos preparando nuevas categorías y secciones para la comunidad.</p>
            <a href="{{ route('index') }}" class="btn btn--outline">Volver a Inicio</a>
        </div>
    @endforelse

    {{-- Call to action - Añadir bot de discord a servidor --}}
    <div class="btn-dc-bot-container">
        @include('partials.buttons._dc_bot')
    </div>

    {{-- Iconos de redes sociales --}}
    <section class="mt-3 mb-3">
        @include('partials._social_icons')
    </section>
@endsection
