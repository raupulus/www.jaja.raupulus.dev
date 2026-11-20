@extends('layouts.master')

@section('title', 'Proyecto ' . $project->title . ' del colaborador ' . $collaborator->name . ' @' . $collaborator->nick)
@section('description', \Illuminate\Support\Str::limit(trim(strip_tags((string) $project->excerpt)), 155))
@section('keywords', $project->keywords . ', ' . $project->type . ', ' . $project->repository_type)
@section('og_type', 'article')

@if ($project->image)
    @section('og_image', $project->urlImage)
    @section('twitter_image', $project->urlImage)
@endif

@section('css')
    @vite(['resources/css/page.css', 'resources/css/projects.css'])
@endsection

@push('jsonld')
    @include('partials._jsonld', [
        'schema' => array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $project->title,
            'description' => $project->excerpt,
            'url' => route('collaborator.project.show', [$collaborator->nick, $project->slug]),
            'applicationCategory' => $project->type,
            'image' => $project->urlImage,
            'author' => [
                '@type' => 'Person',
                'name' => $collaborator->name,
                'url' => route('collaborator.show', $collaborator->nick),
            ],
        ]),
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
                [
                    '@type' => 'ListItem',
                    'position' => 4,
                    'name' => $project->title,
                    'item' => route('collaborator.project.show', [$collaborator->nick, $project->slug]),
                ],
            ],
        ],
    ])
@endpush

@section('content')
    <div class="page-container">
        <div class="text-left">
            <a href="{{route('collaborator.show', $collaborator->nick)}}">
                <span class="icon-back"><-</span>
                Volver a los proyectos
            </a>
        </div>

        <div class="page-header">
            <h1 class="page-title">Proyecto {{$project->title}}</h1>
            {{-- Badges --}}
            <p class="badge-metadata">
                {{-- Tipo de proyecto o colaboración: ['web', 'mobile', 'desktop', 'bot', 'marketing', 'other']--}}
                <span>{{$project->type}}</span>
                {{-- Tipo de repositorio: ['github', 'gitlab', 'bitbucket', 'other']--}}
                <span>{{$project->repository_type}}</span>
            </p>

            <p>
                Colaborador: {{$collaborator->name . '(@' . $collaborator->nick . ')'}}
            </p>

            {{-- Palabras clave como badges individuales --}}
            @if($project->keywords)
                <p>
                    @foreach(array_map('trim', explode(',', $project->keywords)) as $keyword)
                        @if($keyword)
                            <span class="keyword-badge">{{$keyword}}</span>
                        @endif
                    @endforeach
                </p>
            @endif

            <p>
                {{$collaborator->description}}
            </p>

            @if($project->image)
                <div class="project-image">
                    <img src="{{$project->urlImage}}" alt="{{$project->name}}">
                </div>
            @endif

        </div>

        <section class="page-content">
            <div class="page-content-wrapper">
                {!! $project->getHtmlContent() !!}
            </div>
        </section>

        @include('collaborators.partials._projects', $projects)

    </div>
@endsection
