<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use softDeletes;

    protected $table = 'categories';
    protected $fillable = ['title', 'description', 'image', ];


    /**
     * Relación con los contenidos de una categoría.
     *
     * @return BelongsToMany
     */
    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_categories');
    }


    /**
     * Relación con las sugerencias de una categoría.
     *
     * @return BelongsToMany
     */
    public function suggestions(): BelongsToMany
    {
        return $this->belongsToMany(Suggestion::class, 'suggestion_categories');
    }


    /**
     * Devuelve la url hacia la imagen.
     *
     * @return string
     */
    public function getUrlImageAttribute(): string
    {
        if (!$this->image || ($this->image === 'images/default/category.webp')) {
            return asset('images/default/category.webp');
        }

        return asset('storage/' . $this->image);
    }
}
