<nav class="breadcrumbs" aria-label="Ruta de navegación">
    <ol class="breadcrumbs-list">
        @foreach($items as $item)
            <li class="breadcrumbs-item">
                @if(!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @else
                    <span @if($loop->last) aria-current="page" @endif>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
