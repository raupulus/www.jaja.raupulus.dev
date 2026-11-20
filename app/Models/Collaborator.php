<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Collaborator
 *
 * Representa un colaborador de software o creador de contenido que aporta al proyecto.
 */
class Collaborator extends Model
{
    use DeletesStoredImages;

    protected $table = 'collaborators';

    protected $fillable = ['user_id', 'name', 'nick', 'website', 'image', 'description', 'url_repositories'];

    /**
     * Elimina también las imágenes de los proyectos asociados cuando se borra el colaborador.
     */
    protected static function booted(): void
    {
        static::deleting(function (Collaborator $collaborator): void {
            $collaborator->projects()
                ->whereNotNull('image')
                ->pluck('image')
                ->each(fn (string $imagePath) => $collaborator->deleteStoredImageFile($imagePath));
        });
    }

    /**
     * Atributos de imagen gestionados en Storage::disk('public').
     *
     * @return array<string, string|null>
     */
    protected function storedImageAttributes(): array
    {
        return ['image' => null];
    }

    /**
     * Usuario asociado al colaborador.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Proyectos asociados al colaborador.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(CollaboratorProject::class, 'collaborator_id', 'id');
    }

    /**
     * Devuelve la url hacia la imagen si la tuviera.
     */
    public function getUrlImageAttribute(): string
    {
        return $this->image ? asset('storage/'.$this->image) : asset('images/default/avatar.webp');
    }

    /**
     * Devuelve los colaboradores con proyectos verificados.
     *
     * @return mixed
     */
    public static function getCollaboratorsVerified()
    {
        return self::select('collaborators.*', \DB::raw('COUNT(collaborator_projects.id) as projects_count'))
            ->leftJoin('collaborator_projects', 'collaborators.id', '=', 'collaborator_projects.collaborator_id')
            ->where('collaborator_projects.status', 'published')
            ->groupBy('collaborators.id')
            ->havingRaw('COUNT(collaborator_projects.id) > 0')
            ->get();
    }
}
