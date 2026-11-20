@extends('layouts.master')

@section('title', 'Colaborador ' . $collaborator->name . ' @' . $collaborator->nick)
@section('description', \Illuminate\Support\Str::limit(trim(strip_tags((string) $collaborator->description)), 155))
@section('keywords', $collaborator->nick . ', colaborador, contribuidor, jaja project')
@section('og_type', 'profile')

@if ($collaborator->image)
    @section('og_image', $collaborator->urlImage)
    @section('twitter_image', $collaborator->urlImage)
@endif

@section('css')
    @vite(['resources/css/page.css', 'resources/css/projects.css'])
@endsection

@push('jsonld')
    @include('partials._jsonld', [
        'schema' => [
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            'url' => route('collaborator.show', $collaborator->nick),
            'inLanguage' => 'es',
            'mainEntity' => array_filter([
                '@type' => 'Person',
                'name' => $collaborator->name,
                'alternateName' => '@' . $collaborator->nick,
                'description' => $collaborator->description,
                'url' => route('collaborator.show', $collaborator->nick),
                'image' => $collaborator->urlImage,
                'sameAs' => array_values(array_filter([$collaborator->website, $collaborator->url_repositories])),
            ]),
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
                    'name' => 'Colaboradores',
                    'item' => route('collaborator.index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $collaborator->name,
                    'item' => route('collaborator.show', $collaborator->nick),
                ],
            ],
        ],
    ])
@endpush

@section('content')
    <div class="page-container">
        <div class="page-header">
            <h1 class="page-title">Colaborador {{$collaborator->name . '(@' . $collaborator->nick . ')'}}</h1>
            <div>
                <img src="{{$collaborator->urlImage}}" alt="{{$collaborator->name}}">
            </div>
            <p class="page-description">
                {{$collaborator->description}}
            </p>
        </div>

        @include('collaborators.partials._projects', $projects)
    </div>
@endsection

