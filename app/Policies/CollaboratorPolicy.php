<?php

namespace App\Policies;

use App\Models\Collaborator;
use App\Models\User;

/**
 * Policy para el modelo Collaborator.
 */
class CollaboratorPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Collaborator $collaborator): bool
    {
        return $collaborator->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return ! $user->collaborator()->exists();
    }

    public function update(User $user, Collaborator $collaborator): bool
    {
        return $collaborator->user_id === $user->id;
    }

    public function delete(User $user, Collaborator $collaborator): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
