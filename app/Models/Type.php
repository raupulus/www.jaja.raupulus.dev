<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use softDeletes;

    protected $table = 'types';

    protected $fillable = ['name', 'description', 'image', ];

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
