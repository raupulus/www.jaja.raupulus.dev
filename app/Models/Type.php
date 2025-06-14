<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use softDeletes;

    protected $table = 'types';

    protected $fillable = ['name', 'slug', 'description', 'image', ];

    /**
     * Grupos asociados al tipo.
     *
     * @return HasMany
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'type_id', 'id');
    }

    /**
     * Relación con las categorías a través de contenidos
     */
    public function categories(): HasManyThrough
    {
        return $this->hasManyThrough(
            \App\Models\Content::class,
            \App\Models\Group::class,
            'type_id',  // Foreign key en groups que apunta a types
            'group_id', // Foreign key en contents que apunta a groups
            'id',       // Local key en types
            'id'        // Local key en groups
        );








    }



    /**
     * Devuelve la url hacia la imagen.
     *
     * @return string
     */
    public function getUrlImageAttribute(): string
    {
        if (!$this->image || ($this->image === 'images/default/type.webp')) {
            return asset('images/default/type.webp');
        }

        return asset('storage/' . $this->image);
    }
}
