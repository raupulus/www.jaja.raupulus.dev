<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\CollaboratorProject;
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
        $collaborators = Collaborator::getCollaboratorsVerified();

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
        $projectsCount = $collaborator?->projects()->where('status', 'published')->count();

        if (!$collaborator?->id || !$projectsCount) {
            abort(404);
        }

        return view('collaborators.show')->with([
            'collaborator' => $collaborator,
            'projectsCount' => $projectsCount,
            'projects' => $collaborator->projects()->where('status', 'published')->get(),
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

        $projects = $collaborator->projects()
            ->where('status', 'published')
            ->whereNot('id', $project->id)
            ->get();

        return view('collaborators.projects.show')->with([
            'collaborator' => $collaborator,
            'project' => $project,
            'projects' => $projects,
        ]);
    }
}
