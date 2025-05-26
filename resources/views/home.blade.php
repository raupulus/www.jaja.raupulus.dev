@extends('layouts.master')

@section('content')
    <section>
        <h1>Bienvenid@ a {{config('app.name')}}</h1>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
            dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex
            ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat
            nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit
            anim id est laborum.
        </p>
    </section>

    <section class="card">
        <h2 class="card-title">Sección Destacada</h2>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
            dolore magna aliqua.
        </p>
        <a href="#" class="btn">Saber Más</a>
    </section>

    <section class="card">
        <h2 class="card-title">Bot para Twitch</h2>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
            dolore magna aliqua.
        </p>
        <a href="#" class="btn">Saber Más</a>
    </section>

    <section class="card">
        <h2 class="card-title">Envía tu <br/>chiste/adivinanza</h2>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
            dolore magna aliqua.
        </p>

        <div>
            <form action="#" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="form-group">
                    <input type="text" name="nick" placeholder="Tu Nick (Sin @)" class="form-control" value="{{old('nick')}}">

                    <select name="type_id" class="form-control">
                        @foreach(\App\Models\Type::all() as $type)
                            <option value="{{$type->id}}" {{(old('type_id') === $type->id) ? 'selected' : ''}}>{{$type->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="content-image">Imagen (Opcional)</label>
                    <input id="content-image" type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <input type="text" name="title" placeholder="Título" class="form-control" required value="{{old('title')}}">
                </div>

                <div class="form-group">
                    <textarea name="description" class="form-control" placeholder="Escribe aquí el contenido" required>{{old('description')}}</textarea>
                </div>

                <button type="submit" class="btn">Enviar</button>
            </form>
        </div>
    </section>

    <section class="box-card-content">
        <h2>Algunos contenidos</h2>

        @foreach(\App\Models\Content::inRandomOrder()->take(10)->get() as $content)
            <div class="card card-content">
                @if ($content->image)
                    <div class="card-content-image">
                        <img src="{{$content->urlImage}}" title="{{$content->title}}" alt="{{$content->title}}"/>
                    </div>
                @endif

                <div class="card-content-body">
                    <h3>{{$content->title}}</h3>

                    <p>
                        {{$content->content}}
                    </p>
                </div>

                <div class="card-content-meta">
                    <span>{{$content->group?->title}}</span>
                    <span>Por: {{$content->uploader}}</span>
                </div>

            </div>
        @endforeach
    </section>
@endsection
