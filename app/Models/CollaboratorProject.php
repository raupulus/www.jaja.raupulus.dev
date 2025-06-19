<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollaboratorProject extends Model
{
    protected $table = 'collaborator_projects';

    protected $fillable = ['collaborator_id', 'title', 'slug', 'url', 'image', 'excerpt', 'content', 'type',
        'repository_type', 'keywords', 'status'];

    /**
     * Relación con el colaborador.
     *
     * @return BelongsTo
     */
    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class, 'collaborator_id', 'id');
    }

    /**
     * Devuelve la url hacia la imagen si la tuviera.
     *
     * @return string|null
     */
    public function getUrlImageAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Parsea el contenido en markdown y lo devuelve en formato HTML
     *
     * @return string
     */
    public function getHtmlContent(): string
    {
        $parsedown = new \Parsedown();
        $parsedown->setSafeMode(true);

        return $parsedown->text($this->content);
    }
}
