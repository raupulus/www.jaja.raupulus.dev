<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuggestionOption extends Model
{
    protected $table = 'suggestion_options';

    protected $fillable = ['suggestion_id', 'value', 'is_correct', 'image_path', 'order'];

    /**
     * Relación con el contenido asociado.
     *
     * @return BelongsTo
     */
    public function suggestion(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'suggestion_id', 'id');
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
