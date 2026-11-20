<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\CollaboratorProject;
use App\Services\CacheInvalidationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Controlador CollaboratorController
 *
 * Gestiona la visualización pública de perfiles de colaboradores y sus proyectos.
 */
class CollaboratorController extends Controller
{
    /**
     * Muestra el listado de colaboradores en la plataforma.
     */
    public function index(): View
    {
        $collaborators = Cache::remember('page_collaborators_index', 600, function () {
            return Collaborator::getCollaboratorsVerified();
        });

        return view('collaborators.index')->with([
            'collaborators' => $collaborators,
        ]);
    }

    /**
     * Muestra un colaborador con todos sus proyectos.
     *
     * @param  Collaborator  $collaborator  Colaborador
     */
    public function show(Collaborator $collaborator): View
    {
        if (! $collaborator->exists) {
            abort(404);
        }

        $projectsCount = Cache::remember('page_collaborator_projects_count_'.$collaborator->id, 600, function () use ($collaborator) {
            return $collaborator->projects()->where('status', 'published')->count();
        });

        if (! $projectsCount) {
            abort(404);
        }

        $projects = Cache::remember('page_collaborator_'.$collaborator->id.'_projects', 600, function () use ($collaborator) {
            return $collaborator->projects()->where('status', 'published')->get();
        });

        return view('collaborators.show')->with([
            'collaborator' => $collaborator,
            'projectsCount' => $projectsCount,
            'projects' => $projects,
        ]);
    }

    /**
     * Muestra un proyecto concreto de un colaborador
     *
     * @param  Collaborator  $collaborator  Colaboraor
     * @param  CollaboratorProject  $project  Proyecto
     */
    public function showProject(Collaborator $collaborator, CollaboratorProject $project): View
    {
        if (
            ! $collaborator->exists
            || ! $project->exists
            || $project->collaborator_id !== $collaborator->id
            || $project->status !== 'published'
        ) {
            abort(404);
        }

        $projects = Cache::remember(
            CacheInvalidationService::collaboratorShowProjectKey($collaborator->id, $project->id),
            600,
            function () use ($collaborator, $project) {
                return $collaborator->projects()
                    ->where('status', 'published')
                    ->whereNot('id', $project->id)
                    ->get();
            }
        );

        return view('collaborators.projects.show')->with([
            'collaborator' => $collaborator,
            'project' => $project,
            'projects' => $projects,
        ]);
    }
}
