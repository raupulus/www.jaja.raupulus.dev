@extends('layouts.master')

@section('title', 'Listados de Categorías - ' . config('app.name'))
@section('description', 'Explora todas las categorías temáticas de chistes, adivinanzas y preguntas quiz disponibles en la comunidad de ' . config('app.name') . '.')
@section('keywords', 'categorías, categoría, comunidad, lista de categorías, listado de categorías')

@section('css')
    @vite(['resources/css/social_icons.css', 'resources/css/grid_categories.css'])
@endsection

@push('jsonld')
    @include('partials._jsonld', [
        'schema' => [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Categorías de contenido - ' . config('app.name'),
            'url' => route('content.categories.index'),
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
                    'name' => 'Categorías',
                    'item' => route('content.categories.index'),
                ],
            ],
        ],
    ])
@endpush

@section('content')
    @include('partials._breadcrumbs', [
        'items' => [
            ['label' => 'Inicio', 'url' => route('index')],
            ['label' => 'Categorías'],
        ],
    ])

    <h1 class="text-center">Categorías de contenido</h1>

    @if($categories->isNotEmpty())
        <section class="category-grid">
            @foreach($categories as $category)
                <a href="{{route('content.categoria.content.random', $category->slug)}}" class="category-card">
                    <div class="category-card-content {{ $category->image ? '' : 'no-image' }}">
                        @if($category->image)
                            <div class="category-card-image">
                                <img src="{{$category->urlImage}}" alt="{{$category->title}}" width="60" height="60" loading="lazy" decoding="async">
                            </div>
                        @endif

                        <div class="category-card-text">
                            <h2 class="category-card-title">
                                {{ Str::limit($category->title, 80) }}
                            </h2>

                            @if($category->description)
                                <p class="category-card-description">
                                    {{ $category->description }}
                                </p>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </section>
    @else
        <div class="empty-state">
            <h2>No hay categorías con contenido disponible todavía</h2>
            <p>Vuelve pronto o envía tu propia sugerencia desde la página principal.</p>
            <a href="{{ route('index') }}" class="btn btn--outline">Volver a Inicio</a>
        </div>
    @endif

    {{-- Call to action - Añadir bot de discord a servidor --}}
    <div class="btn-dc-bot-container">
        @include('partials.buttons._dc_bot')
    </div>

    {{-- Iconos de redes sociales --}}
    <section class="mt-3 mb-3">
        @include('partials._social_icons')
    </section>
@endsection
