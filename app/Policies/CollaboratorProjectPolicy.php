<?php

namespace App\Policies;

use App\Models\CollaboratorProject;
use App\Models\User;

/**
 * Policy para el modelo CollaboratorProject.
 */
class CollaboratorProjectPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CollaboratorProject $collaboratorProject): bool
    {
        return $collaboratorProject->collaborator?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CollaboratorProject $collaboratorProject): bool
    {
        return $collaboratorProject->collaborator?->user_id === $user->id;
    }

    public function delete(User $user, CollaboratorProject $collaboratorProject): bool
    {
        return $collaboratorProject->collaborator?->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return true;
    }
}
