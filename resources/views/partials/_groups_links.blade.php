{{-- Enlaces rápidos a secciones principales --}}
<section class="groups-links">
    <a href="{{ route('content.types.index') }}" class="groups-link-card tipos">
        <div class="groups-link-content">
            <div class="groups-link-indicator"></div>
            <h3 class="groups-link-title">Tipos</h3>
            <div class="groups-link-count">{{ $typesCount ?? 0 }}</div>
        </div>
    </a>

    <a href="{{ route('content.groups.index') }}" class="groups-link-card grupos">
        <div class="groups-link-content">
            <div class="groups-link-indicator"></div>
            <h3 class="groups-link-title">Grupos</h3>
            <div class="groups-link-count">{{ $groupsCount ?? 0 }}</div>
        </div>
    </a>

    <a href="{{ route('content.categories.index') }}" class="groups-link-card tags">
        <div class="groups-link-content">
            <div class="groups-link-indicator"></div>
            <h3 class="groups-link-title">Tags</h3>
            <div class="groups-link-count">{{ $categoriesCount ?? 0 }}</div>
        </div>
    </a>
</section>
