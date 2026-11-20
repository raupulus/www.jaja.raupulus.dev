@extends('layouts.master')

@section('title', 'Listado de páginas en ' . config('app.name'))
@section('description', 'Explora todas las páginas informativas, guías de la API, bots comunitarios, normas y documentación legal de ' . config('app.name') . '.')
@section('keywords', 'páginas, documentación, normas, api, bots, agradecimientos, ' . config('app.name'))

@section('css')
    @vite(['resources/css/pages.css'])
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
        <div class="page-header">
            <h1 class="page-title">Listado de páginas en {{config('app.name')}}</h1>
        </div>

        <section>
            @foreach($pages as $page)

                <div class="card card-content">
                    <div class="card-content-image">
                    @if ($page->image)
                            <img src="{{$page->urlImageThumbnail}}" title="{{$page->title}}" alt="{{$page->title}}"/>
                    @endif
                    </div>

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


                </div>

            @endforeach
        </section>
    </div>



@endsection
