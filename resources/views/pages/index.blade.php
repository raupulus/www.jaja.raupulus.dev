@extends('layouts.master')

@section('title', 'Listado de páginas en ' . config('app.name'))
@section('description', '')
@section('keywords', '')

@section('css')
    @vite(['resources/css/pages.css'])
@endsection

@section('content')

    <div class="page-container">
        <div class="page-header">
            <h1 class="page-title">Listado de páginas en {{config('app.name')}}</h1>
        </div>

        <section>
            @foreach($pages as $page)

                <div class="card card-content">
                    <div class="card-content-image">
                    @if ($page->image)
                            <img src="{{$page->urlImageThumbnail}}" title="{{$page->title}}" alt="{{$page->title}}"/>
                    @endif
                    </div>

                    <div class="card-content-body">
                        <h3>{{$page->title}}</h3>

                        <p>
                            {{$page->excerpt}}
                        </p>

                        <div class="card-content-meta">
                            @foreach(array_map('trim', explode(',', $page->keywords)) as $keyword)
                                @if($keyword)
                                    <span>{{$keyword}}</span>
                                @endif
                            @endforeach
                        </div>

                        <a href="{{$page->url}}" title="{{$page->title}}" class="btn">
                            Ir
                        </a>
                    </div>


                </div>

            @endforeach
        </section>
    </div>



@endsection
