@extends('layouts.master')

@section('title', 'Página no encontrada - ' . config('app.name'))
@section('description', 'La página o recurso que buscas no se encuentra disponible.')
@section('robots', 'noindex, nofollow')

@section('content')
    <section class="empty-state">
        <h1>404 · Página no encontrada</h1>
        <p>
            El enlace al que intentas acceder no existe o ya no tiene contenido público disponible.
        </p>
        <div class="card-content-meta">
            <a href="{{ route('index') }}" class="btn">Ir a Inicio</a>
            <a href="{{ route('content.groups.index') }}" class="btn btn--outline">Explorar Grupos</a>
            <a href="{{ route('content.categories.index') }}" class="btn btn--outline">Explorar Categorías</a>
        </div>
    </section>
@endsection
