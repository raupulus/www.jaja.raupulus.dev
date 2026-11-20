@extends('layouts.master')

@section('title', 'Contenidos aleatorios del grupo ' . $group->title)
@section('description', \Illuminate\Support\Str::limit(trim(strip_tags((string) $group->description)) ?: 'Descubre contenidos aleatorios del grupo ' . $group->title . ' en ' . config('app.name') . '.', 155))
@section('keywords', $group->title . ', chistes, adivinanzas, preguntas, contenido aleatorio')

@section('css')
    @vite(['resources/css/social_icons.css'])
@endsection

@push('jsonld')
    @include('partials._jsonld', [
        'schema' => [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $group->title,
            'description' => $group->description,
            'url' => route('content.group.content.random', $group->slug),
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
                    'name' => 'Grupos',
                    'item' => route('content.groups.index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $group->title,
                    'item' => route('content.group.content.random', $group->slug),
                ],
            ],
        ],
    ])
@endpush

@section('content')
    <div class="text-left">
        <a href="{{route('content.groups.index')}}">
            <span class="icon-back"><-</span>
            Ir a los Grupos
        </a>
    </div>

    <div>
        <div>
            <h1>{{$group->title}}</h1>
            <p>
                {{ $group->description }}
            </p>

            @if($group->image)
                <img src="{{$group->urlimage}}" alt="{{$group->title}}" class="media-banner-md" width="500" height="333" loading="lazy" decoding="async">
            @endif
        </div>

        <div id="box-btn-random">
            <div class="btn-random-content">
                <a href="{{route('content.group.content.random', $group->slug)}}#box-btn-random"
                   onclick="window.location.reload(); return false;">
                    <div class="wheel">
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                    </div>
                    <span class="text">Random</span>
                </a>
            </div>
        </div>

        <div>
            @include('contents.partials._contents', ['contents' => $contents])
        </div>
    </div>


    {{-- Call to action - Añadir bot de discord a servidor --}}
    <div class="btn-dc-bot-container">
        @include('partials.buttons._dc_bot')
    </div>

    {{-- Iconos de redes sociales --}}
    <section class="mt-3 mb-3">
        @include('partials._social_icons')
    </section>

@endsection
