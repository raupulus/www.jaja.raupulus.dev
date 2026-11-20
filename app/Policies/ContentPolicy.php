<?php

namespace App\Policies;

use App\Models\Content;
use App\Models\User;

/**
 * Policy para el modelo Content.
 */
class ContentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Content $content): bool
    {
        return $content->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Content $content): bool
    {
        return $content->user_id === $user->id;
    }

    public function delete(User $user, Content $content): bool
    {
        return $content->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return true;
    }

    public function restore(User $user, Content $content): bool
    {
        return $content->user_id === $user->id;
    }

    public function restoreAny(User $user): bool
    {
        return true;
    }

    public function forceDelete(User $user, Content $content): bool
    {
        return $content->user_id === $user->id;
    }

    public function forceDeleteAny(User $user): bool
    {
        return true;
    }
}
