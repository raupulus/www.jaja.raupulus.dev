<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\CollaboratorProject;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CollaboratorController extends Controller
{
    /**
     * Muestra el listado de colaboradores en la plataforma.
     *
     * @return View
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
     * @param Collaborator $collaborator Colaborador
     * @return View
     */
    public function show(Collaborator $collaborator): View
    {
        if (!$collaborator?->id) {
            abort(404);
        }

        $projectsCount = Cache::remember('page_collaborator_projects_count_' . $collaborator->id, 600, function () use ($collaborator) {
            return $collaborator?->projects()->where('status', 'published')->count();
        });

        if (!$projectsCount) {
            abort(404);
        }

        $projects = Cache::remember('page_collaborator_' . $collaborator->id . '_projects', 600, function () use ($collaborator) {
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
     * @param Collaborator $collaborator Colaboraor
     * @param CollaboratorProject $project Proyecto
     * @return View
     */
    public function showProject(Collaborator $collaborator, CollaboratorProject $project): View
    {
        if (!$collaborator?->id || !$project?->id) {
            abort(404);
        }

        $projects = Cache::remember('page_collaborator'. $collaborator->id . '_show_project_' . $project->id, 600, function () use ($collaborator, $project) {
            return $collaborator->projects()
                ->where('status', 'published')
                ->whereNot('id', $project->id)
                ->get();
        });

        return view('collaborators.projects.show')->with([
            'collaborator' => $collaborator,
            'project' => $project,
            'projects' => $projects,
        ]);
    }
}
