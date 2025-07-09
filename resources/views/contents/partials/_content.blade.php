<div class="card card-content">
    @if ($content->image)
        <div class="card-content-image">
            <img src="{{$content->urlImage}}" title="{{$content->title}}" alt="{{$content->title}}"/>
        </div>
    @endif

    <div class="card-content-body">
        <h3>{{$content->title}}</h3>

        <p>
            {!! $content->formattedHtmlContent !!}
        </p>

        @if(($content->group?->type?->slug === 'quiz') && $content->options->count())
           <ol style="margin-left: 3rem;">
            @foreach ($content->options()->orderBy('order')->get() as $option)
                <li>{{$option->value}}</li>
            @endforeach
           </ol>
        @endif

    </div>

    <div class="card-content-meta">
        <span>{{$content->group?->title}}</span>
        <span>Por: {{$content->uploader}}</span>
    </div>

</div>
