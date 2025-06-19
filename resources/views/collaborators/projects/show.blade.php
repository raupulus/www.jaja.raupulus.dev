@extends('layouts.master')

@section('title', 'Proyecto ' . $project->title . ' del colaborador ' . $collaborator->name . ' @' . $collaborator->nick)
@section('description', $project->excerpt)
@section('keywords', $project->keywords . ', ' . $project->type . ', ' . $project->repository_type)

@if ($project->image)
    @section('og_image', $project->urlImage)
    @section('twitter_image', $project->urlImage)
@endif

@section('css')
    @vite(['resources/css/pages.css', 'resources/css/projects.css'])
@endsection

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
