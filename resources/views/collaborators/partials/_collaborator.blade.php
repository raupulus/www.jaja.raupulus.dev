<div class="desarrollador-card">
    <div class="dev-avatar">
        <img src="{{$collaborator->urlImage}}"
             alt="Avatar desarrollador {{$collaborator->name}} ({{'@' . $collaborator->nick}})"
             width="100"
             height="100"
             loading="lazy"
             decoding="async">
    </div>

    <h3 class="dev-name">{{$collaborator->name}}</h3>

    <p class="dev-nick">{{$collaborator->nick}}</p>

    <div class="dev-links">
        @if ($collaborator->website)
            <a href="{{$collaborator->website}}"
               title="Enlace al sitio web personal de {{$collaborator->name}} ({{'@' . $collaborator->nick}})"
               class="dev-link"
               target="_blank"
               rel="noopener noreferrer">
                🌐 Website
            </a>
        @endif

        @if ($collaborator->url_repositories)
            <a href="{{$collaborator->url_repositories}}"
               title="Enlace al perfil de repositorios personal de {{$collaborator->name}} ({{'@' . $collaborator->nick}})"
               class="dev-link"
               target="_blank"
               rel="noopener noreferrer">
                🔗 Repositorios
            </a>
        @endif
    </div>

    <a href="{{route('collaborator.show', $collaborator->nick)}}" class="view-projects-link">
        Ver Proyectos
    </a>
</div>
