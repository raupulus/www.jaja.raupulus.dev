@extends('layouts.master')

@section('title', 'Colaboradores de desarrollo - ' . config('app.name'))
@section('description', 'Conoce a todos los colaboradores que han contribuido al desarrollo de la plataforma: desarrolladores, diseñadores, documentación, marketing y más.')
@section('keywords', 'colaboradores, desarrolladores, open source, contribuidores, desarrollo, programación, ' . config('app.name'))

@section('css')
    @vite(['resources/css/collaborators.css'])
@endsection

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
