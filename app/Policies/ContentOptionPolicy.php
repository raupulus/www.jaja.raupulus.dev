<?php

namespace App\Policies;

use App\Models\ContentOption;
use App\Models\User;

/**
 * Policy para el modelo ContentOption.
 */
class ContentOptionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ContentOption $contentOption): bool
    {
        return $contentOption->content?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ContentOption $contentOption): bool
    {
        return $contentOption->content?->user_id === $user->id;
    }

    public function delete(User $user, ContentOption $contentOption): bool
    {
        return $contentOption->content?->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return true;
    }
}
