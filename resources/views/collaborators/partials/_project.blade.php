<div class="project-item">
    @if($project->image)
        <div>
            <img src="{{$project->urlImage}}" alt="{{$project->title}}">
        </div>
    @else
        <div>
            <div>Sin imagen</div>
        </div>
    @endif

    <div>
        <div>{{$project->title}}</div>
        <div>{{$project->excerpt}}</div>

        @if ($project->url)
            <div>{{$project->url}}</div>
        @endif

        {{-- Badges --}}
        <div>
            {{-- Tipo de proyecto o colaboración: ['web', 'mobile', 'desktop', 'bot', 'marketing', 'other']--}}
            <span>{{$project->type}}</span>
            {{-- Tipo de repositorio: ['github', 'gitlab', 'bitbucket', 'other']--}}
            <span>{{$project->repository_type}}</span>
        </div>

        {{-- Palabras clave como badges individuales --}}
        @if($project->keywords)
            <div class="keywords-container">
                @foreach(array_map('trim', explode(',', $project->keywords)) as $keyword)
                    @if($keyword)
                        <span class="keyword-badge">{{$keyword}}</span>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <a href="{{route('collaborator.project.show', [$collaborator->nick, $project->slug])}}">
        Ver proyecto
    </a>

</div>
