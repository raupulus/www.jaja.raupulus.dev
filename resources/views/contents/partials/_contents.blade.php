<section class="box-card-content">

    @if(isset($contents_title) && $contents_title)
        <h2>{{$contents_title}}</h2>
    @endif

    @php
        $contentHeadingTag = ! empty($contents_title) ? 'h3' : 'h2';
    @endphp

    @foreach($contents as $content)

        @include('contents.partials._content', ['contentHeadingTag' => $contentHeadingTag])

    @endforeach

</section>
