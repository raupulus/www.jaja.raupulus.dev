<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class Collaborator extends Model
{
    protected $table = 'collaborators';

    protected $fillable = ['user_id', 'name', 'nick', 'website', 'image', 'description'];

    /**
     * Usuario asociado al colaborador.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Proyectos asociados al colaborador.
     *
     * @return HasMany
     */
    public function projects(): HasMany
    {
        return $this->hasMany(CollaboratorProject::class, 'collaborator_id', 'id');
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
}
