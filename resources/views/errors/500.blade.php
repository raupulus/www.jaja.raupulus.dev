@extends('layouts.master')

@section('title', 'Error interno - ' . config('app.name'))
@section('description', 'Ha ocurrido un error inesperado al procesar la solicitud.')
@section('robots', 'noindex, nofollow')

@section('content')
    <section class="empty-state">
        <h1>500 · Error interno del servidor</h1>
        <p>
            Algo ha fallado momentáneamente al procesar tu petición. Por favor, inténtalo de nuevo en unos instantes.
        </p>
        <a href="{{ route('index') }}" class="btn">Volver a Inicio</a>
    </section>
@endsection
