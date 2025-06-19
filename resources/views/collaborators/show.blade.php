@extends('layouts.master')

@section('title', 'Colaborador ' . $collaborator->name . ' @' . $collaborator->nick)
@section('description', $collaborator->description)
@section('keywords', $collaborator->nick . ', colaborador, contribuidor, jaja project')

@if ($collaborator->image)
    @section('og_image', $collaborator->urlImage)
    @section('twitter_image', $collaborator->urlImage)
@endif

@section('css')
    @vite(['resources/css/projects.css'])
@endsection

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

