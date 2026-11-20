<section class="box-card-content">

    @if(isset($contents_title) && $contents_title)
        <h2>{{$contents_title}}</h2>
    @endif

    @php
        $contentHeadingTag = ! empty($contents_title) ? 'h3' : 'h2';
    @endphp

    @forelse($contents as $content)

        @include('contents.partials._content', ['contentHeadingTag' => $contentHeadingTag])

    @empty
        <div class="empty-state">
            <{{ $contentHeadingTag }}>No hay contenidos disponibles en esta sección</{{ $contentHeadingTag }}>
            <p>Sé el primero en aportar un chiste, adivinanza o pregunta quiz desde la portada.</p>
            <a href="{{ route('index') }}" class="btn btn--outline">Enviar una sugerencia</a>
        </div>
    @endforelse

</section>
