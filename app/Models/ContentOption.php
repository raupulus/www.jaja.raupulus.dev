<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ContentOption
 *
 * Representa las opciones de respuesta para un contenido de tipo Quiz.
 */
class ContentOption extends Model
{
    use DeletesStoredImages;

    protected $table = 'content_options';

    protected $fillable = ['content_id', 'value', 'is_correct', 'image_path', 'order'];

    /**
     * Atributos de imagen gestionados en Storage::disk('public').
     *
     * @return array<string, string|null>
     */
    protected function storedImageAttributes(): array
    {
        return ['image_path' => null];
    }

    /**
     * Relación con el contenido asociado.
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id', 'id');
    }

    /**
     * Devuelve la url hacia la imagen si tuviera.
     */
    public function getUrlImageAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
