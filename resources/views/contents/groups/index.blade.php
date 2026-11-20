@extends('layouts.master')

@php
    if (isset($type)) {
        $groupsTitle = 'Grupos de ' . $type->name . ' - ' . config('app.name');
        $groupsHeading = 'Grupos de ' . $type->name;
        $groupsDescription = \Illuminate\Support\Str::limit(
            'Explora los grupos de ' . mb_strtolower($type->name) . ' en ' . config('app.name') . ': ' . ($type->description ?: 'contenido comunitario clasificado para descubrir y compartir.'),
            155
        );
    } else {
        $groupsTitle = 'Grupos de contenido - ' . config('app.name');
        $groupsHeading = 'Grupos de contenido';
        $groupsDescription = 'Explora todos los grupos de chistes, adivinanzas y preguntas quiz creados por la comunidad de ' . config('app.name') . ' para descubrir contenido al instante.';
    }
@endphp

@section('title', $groupsTitle)
@section('description', $groupsDescription)
@section('keywords', implode(',', $groups->pluck('title')->toArray()))

@section('css')
    @vite(['resources/css/social_icons.css', 'resources/css/horizontal_card.css'])
@endsection

@push('jsonld')
    @php
        $groupsUrl = isset($type)
            ? route('content.type.group.index', $type->slug)
            : route('content.groups.index');
        $breadcrumbItems = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Inicio',
                'item' => route('index'),
            ],
        ];
        if (isset($type)) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Tipos',
                'item' => route('content.types.index'),
            ];
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => 'Grupos de ' . $type->name,
                'item' => $groupsUrl,
            ];
        } else {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Grupos',
                'item' => $groupsUrl,
            ];
        }
    @endphp
    @include('partials._jsonld', [
        'schema' => [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => isset($type) ? 'Grupos de ' . $type->name : 'Grupos de contenido',
            'url' => $groupsUrl,
            'inLanguage' => 'es',
        ],
    ])
    @include('partials._jsonld', [
        'schema' => [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ],
    ])
@endpush

@section('content')
    <h1 class="text-center">{{ $groupsHeading }}</h1>

    @foreach($groups as $group)

        <div class="card-resume-horizontal">
            <div class="card-resume-left">
                <h2 class="card-resume-title">{{$group->title}}</h2>

                @if($group->image)
                    <div class="card-resume-image">
                        <img src="{{$group->urlImage}}" alt="{{$group->title}}" width="220" height="120" loading="lazy" decoding="async">
                    </div>
                @endif
            </div>

            <div class="card-resume-content">
                <p class="card-resume-description">
                    {{$group->description}}
                </p>
                <div class="card-resume-meta">
                    Última actualización: {{$group->last_update_message}}
                </div>
            </div>

            <div class="card-resume-right">
                <span class="card-resume-badge">{{$group->contents_count}}</span>

                @if($group->contents()->count())
                    <a href="{{route('content.group.content.random', $group->slug)}}" class="card-resume-button">
                        Ver contenidos de {{$group->title}}
                    </a>
                @endif
            </div>
        </div>

    @endforeach

    {{-- Call to action - Añadir bot de discord a servidor --}}
    <div class="btn-dc-bot-container">
        @include('partials.buttons._dc_bot')
    </div>

    {{-- Iconos de redes sociales --}}
    <section class="mt-3 mb-3">
        @include('partials._social_icons')
    </section>
@endsection
