<section class="projects-container">
    <div class="projects-header">
        <h2>Proyectos y/o colaboraciones</h2>
    </div>

    <div class="projects-grid">

        @foreach($projects as $project)
            @include('collaborators.partials._project', $project)
        @endforeach
    </div>
</section>
