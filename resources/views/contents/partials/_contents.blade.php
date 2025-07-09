<section class="box-card-content">

    @if(isset($contents_title) && $contents_title)
        <h2>{{$contents_title}}</h2>
    @endif

    @foreach($contents as $content)

        @include('contents.partials._content')

    @endforeach

</section>
