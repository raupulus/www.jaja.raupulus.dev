<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredImages;
use Database\Factories\CollaboratorProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo CollaboratorProject
 *
 * Representa un proyecto o repositorio creado por un colaborador.
 *
 * @property int $id
 * @property int $collaborator_id
 * @property string $title
 * @property string $slug
 * @property string|null $url
 * @property string|null $image
 * @property string $excerpt
 * @property string $content
 * @property string $type
 * @property string $repository_type
 * @property string|null $keywords
 * @property string $status
 * @property-read Collaborator|null $collaborator
 * @property-read string|null $urlImage
 */
class CollaboratorProject extends Model
{
    use DeletesStoredImages;

    /** @use HasFactory<CollaboratorProjectFactory> */
    use HasFactory;

    protected $table = 'collaborator_projects';

    protected $fillable = ['collaborator_id', 'title', 'slug', 'url', 'image', 'excerpt', 'content', 'type',
        'repository_type', 'keywords', 'status'];

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
     * Relación con el colaborador.
     *
     * @return BelongsTo<Collaborator, $this>
     */
    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class, 'collaborator_id', 'id');
    }

    /**
     * Devuelve la url hacia la imagen si la tuviera.
     */
    public function getUrlImageAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }

    /**
     * Parsea el contenido en markdown y lo devuelve en formato HTML
     */
    public function getHtmlContent(): string
    {
        $parsedown = new \Parsedown;
        $parsedown->setSafeMode(true);

        return $parsedown->text($this->content);
    }
}
