@extends('layouts.master')

@section('title', 'Listado de páginas en ' . config('app.name'))
@section('description', 'Explora todas las páginas informativas, guías de la API, bots comunitarios, normas y documentación legal de ' . config('app.name') . '.')
@section('keywords', 'páginas, documentación, normas, api, bots, agradecimientos, ' . config('app.name'))

@section('css')
    @vite(['resources/css/page.css'])
@endsection

@push('jsonld')
    @include('partials._jsonld', [
        'schema' => [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Listado de páginas en ' . config('app.name'),
            'url' => route('page.index'),
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
                    'name' => 'Páginas',
                    'item' => route('page.index'),
                ],
            ],
        ],
    ])
@endpush

@section('content')

    <div class="page-container">
        @include('partials._breadcrumbs', [
            'items' => [
                ['label' => 'Inicio', 'url' => route('index')],
                ['label' => 'Páginas'],
            ],
        ])

        <div class="page-header">
            <h1 class="page-title">Listado de páginas en {{config('app.name')}}</h1>
        </div>

        <section>
            @forelse($pages as $page)

                <article class="card card-content {{ $page->image ? '' : 'card-content--no-image' }}">
                    @if ($page->image)
                        <div class="card-content-image">
                            <img src="{{$page->urlImageThumbnail}}" title="{{$page->title}}" alt="{{$page->title}}" width="120" height="120" loading="lazy" decoding="async"/>
                        </div>
                    @endif

                    <div class="card-content-body">
                        <h2>{{$page->title}}</h2>

                        <p>
                            {{$page->excerpt}}
                        </p>

                        <div class="card-content-meta">
                            @foreach(array_map('trim', explode(',', $page->keywords)) as $keyword)
                                @if($keyword)
                                    <span>{{$keyword}}</span>
                                @endif
                            @endforeach
                        </div>

                        <a href="{{$page->url}}" title="{{$page->title}}" class="btn">
                            Leer {{$page->title}}
                        </a>
                    </div>
                </article>

            @empty
                <div class="empty-state">
                    <h2>Todavía no hay páginas publicadas</h2>
                    <p>Consulta la portada principal para explorar el contenido de la comunidad.</p>
                    <a href="{{ route('index') }}" class="btn btn--outline">Volver a Inicio</a>
                </div>
            @endforelse
        </section>
    </div>



@endsection
