@php
    $verifiedCollaborators = $collaborators ?? \App\Models\Collaborator::getCollaboratorsVerified();
@endphp

<section class="desarrolladores-section">
    <h2 class="desarrolladores-title">Desarrolladores Open Source</h2>
    <p class="desarrolladores-intro">
        Estos colaboradores han contribuido con el desarrollo de esta biblioteca de humor.
    </p>

    @if($verifiedCollaborators->isNotEmpty())
        <div class="desarrolladores-container">
            @foreach($verifiedCollaborators as $collaborator)
                @include('collaborators.partials._collaborator', ['collaborator' => $collaborator])
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <h3>Todavía no hay colaboradores publicados</h3>
            <p>Cuando se publiquen proyectos comunitarios aparecerán listados en esta sección.</p>
            <a href="{{ route('index') }}" class="btn btn--outline">Volver a Inicio</a>
        </div>
    @endif
</section>
