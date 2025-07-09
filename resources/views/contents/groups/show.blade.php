@extends('layouts.master')

@vite(['resources/css/social_icons.css'])

@section('content')
    <div class="text-left">
        <a href="{{route('content.groups.index')}}">
            <span class="icon-back"><-</span>
            Ir a los Grupos
        </a>
    </div>

    <div>
        <div>
            <h1>{{$group->title}}</h1>
            <p>
                {!! $group->description !!}
            </p>

            @if($group->image)
                <img src="{{$group->urlimage}}" alt="{{$group->title}}" style="max-width: 500px;">
            @endif
        </div>

        <div id="box-btn-random">
            <div class="btn-random-content">
                <a href="{{route('content.group.content.random', $group->slug)}}#box-btn-random"
                   onclick="window.location.reload(); return false;">
                    <div class="wheel">
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                        <div class="arrow"></div>
                    </div>
                    <span class="text">Random</span>
                </a>
            </div>
        </div>

        <div>
            @include('contents.partials._contents', ['contents' => $contents])
        </div>
    </div>


    {{-- Call to action - Añadir bot de discord a servidor --}}
    <div class="btn-dc-bot-container">
        @include('partials.buttons._dc_bot')
    </div>

    {{-- Iconos de redes sociales --}}
    <section class="mt-3 mb-3">
        @include('partials._social_icons')
    </section>

@endsection
