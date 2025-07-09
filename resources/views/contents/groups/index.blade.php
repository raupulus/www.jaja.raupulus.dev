@extends('layouts.master')

@section('head')
    @vite(['resources/css/social_icons.css', 'resources/css/horizontal_card.css'])
@endsection

@section('content')
    <h1 class="text-center">Grupos de contenido</h1>

    @foreach($groups as $group)

        <div class="card-resume-horizontal">
            <div class="card-resume-left">
                <h3 class="card-resume-title">{{$group->title}}</h3>

                @if($group->image)
                    <div class="card-resume-image">
                        <img src="{{$group->urlImage}}" alt="{{$group->name}}">
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
                        Ver Algunos
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
