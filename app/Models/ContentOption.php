<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ContentOption
 *
 * Representa las opciones de respuesta para un contenido de tipo Quiz.
 */
class ContentOption extends Model
{
    protected $table = 'content_options';

    protected $fillable = ['content_id', 'value', 'is_correct', 'image_path', 'order'];

    /**
     * Relación con el contenido asociado.
     *
     * @return BelongsTo
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id', 'id');
    }

    /**
     * Devuelve la url hacia la imagen si tuviera.
     *
     * @return string|null
     */
    public function getUrlImageAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
