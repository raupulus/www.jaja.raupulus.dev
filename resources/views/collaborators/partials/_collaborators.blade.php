<section class="desarrolladores-section">
    <h2 class="desarrolladores-title">Desarrolladores Open Source</h2>
    <p class="desarrolladores-intro">
        Estos colaboradores han contribuido con el desarrollo de esta biblioteca de humor.
    </p>

    <div class="desarrolladores-container">

        @foreach(\App\Models\Collaborator::getCollaboratorsVerified() as $collaborator)
            @include('collaborators.partials._collaborator', ['collaborator' => $collaborator])
        @endforeach
    </div>
</section>
