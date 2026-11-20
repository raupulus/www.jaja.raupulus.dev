@extends('layouts.master')

@section('title', 'Colaboradores de desarrollo - ' . config('app.name'))
@section('description', 'Conoce a todos los colaboradores que han contribuido al desarrollo de la plataforma: desarrolladores, diseñadores, documentación, marketing y más.')
@section('keywords', 'colaboradores, desarrolladores, open source, contribuidores, desarrollo, programación, ' . config('app.name'))

@section('css')
    @vite(['resources/css/collaborators.css'])
@endsection

@push('jsonld')
    @include('partials._jsonld', [
        'schema' => [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Colaboradores de desarrollo - ' . config('app.name'),
            'description' => 'Conoce a todos los colaboradores que han contribuido al desarrollo de la plataforma: desarrolladores, diseñadores, documentación, marketing y más.',
            'url' => route('collaborator.index'),
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
                    'name' => 'Colaboradores',
                    'item' => route('collaborator.index'),
                ],
            ],
        ],
    ])
@endpush

@section('content')
    <div class="page-container">
        <div class="page-header">
            <h1 class="page-title">Colaboradores de Desarrollo</h1>
            <p class="page-description">
                Conoce a las personas que han contribuido al desarrollo de esta plataforma a través de código,
                bots, diseño, documentación, marketing y otras formas de colaboración.
            </p>
        </div>

        {{-- Listado de colaboradores --}}
        @include('collaborators.partials._collaborators')
    </div>
@endsection
