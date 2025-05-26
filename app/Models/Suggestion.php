<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suggestion extends Model
{
    protected $table = 'suggestions';

    protected $fillable = ['type_id', 'nick', 'title', 'content', 'image_path', 'ip_address', 'user_agent',];


    /**
     * Relación con el tipo de contenido.
     *
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    /**
     * Devuelve la url hacia la imagen o null si no tuviera.
     *
     * @return string|null
     */
    public function getUrlImageAttribute(): string|null
    {
        return $this->image_path ? asset('storage' . $this->image_path) : null;
    }
}
