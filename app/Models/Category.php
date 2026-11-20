<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Category
 *
 * Representa una categoría temática para clasificar contenidos y sugerencias.
 */
class Category extends Model
{
    use DeletesStoredImages;
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = ['title', 'slug', 'description', 'image'];

    /**
     * Atributos de imagen gestionados en Storage::disk('public').
     *
     * @return array<string, string|null>
     */
    protected function storedImageAttributes(): array
    {
        return ['image' => 'images/default/category.webp'];
    }

    /**
     * Relación con los contenidos de una categoría.
     */
    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_categories');
    }

    /**
     * Relación con las sugerencias de una categoría.
     */
    public function suggestions(): BelongsToMany
    {
        return $this->belongsToMany(Suggestion::class, 'suggestion_categories');
    }

    /**
     * Devuelve la url hacia la imagen.
     */
    public function getUrlImageAttribute(): string
    {
        if (! $this->image || ($this->image === 'images/default/category.webp')) {
            return asset('images/default/category.webp');
        }

        return asset('storage/'.$this->image);
    }

    /**
     * Scope para filtrar categorías que tienen contenidos disponibles públicamente.
     */
    public function scopeHasAvailableContent(Builder $query): Builder
    {
        return $query->whereHas('contents', function (Builder $q): void {
            $q->publicSafe();
        });
    }
}
