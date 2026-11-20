<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Collaborator;
use App\Models\CollaboratorProject;
use App\Models\Content;
use App\Models\Group;
use App\Models\Page;
use App\Models\Suggestion;
use App\Models\Type;
use App\Services\CacheInvalidationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        $this->registerCacheInvalidationListeners();
    }

    /**
     * Registra listeners Eloquent para invalidar cachés cuando cambian entidades clave.
     */
    private function registerCacheInvalidationListeners(): void
    {
        Content::saved(static fn () => CacheInvalidationService::forContent());
        Content::deleted(static fn () => CacheInvalidationService::forContent());
        Content::restored(static fn () => CacheInvalidationService::forContent());

        Suggestion::saved(static fn () => CacheInvalidationService::forSuggestion());
        Suggestion::deleted(static fn () => CacheInvalidationService::forSuggestion());
        Suggestion::restored(static fn () => CacheInvalidationService::forSuggestion());

        Type::saved(static fn (Type $type) => CacheInvalidationService::forType($type));
        Type::deleted(static fn (Type $type) => CacheInvalidationService::forType($type));
        Type::restored(static fn (Type $type) => CacheInvalidationService::forType($type));

        Group::saved(static fn (Group $group) => CacheInvalidationService::forGroup($group));
        Group::deleted(static fn (Group $group) => CacheInvalidationService::forGroup($group));

        Category::saved(static fn () => CacheInvalidationService::forCategory());
        Category::deleted(static fn () => CacheInvalidationService::forCategory());
        Category::restored(static fn () => CacheInvalidationService::forCategory());

        Page::saved(static fn () => CacheInvalidationService::forPage());
        Page::deleted(static fn () => CacheInvalidationService::forPage());

        Collaborator::saved(static fn (Collaborator $collaborator) => CacheInvalidationService::forCollaborator($collaborator->id));
        Collaborator::deleting(static fn (Collaborator $collaborator) => CacheInvalidationService::forCollaborator($collaborator->id));

        CollaboratorProject::saved(static fn (CollaboratorProject $project) => CacheInvalidationService::forCollaborator((int) $project->collaborator_id));
        CollaboratorProject::deleted(static fn (CollaboratorProject $project) => CacheInvalidationService::forCollaborator((int) $project->collaborator_id));
    }
}
