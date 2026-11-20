<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo SuggestionOption
 *
 * Representa las opciones de respuesta sugeridas para un contenido tipo Quiz.
 *
 * @property int $id
 * @property int $suggestion_id
 * @property string $value
 * @property bool $is_correct
 * @property string|null $image_path
 * @property int $order
 * @property-read Suggestion|null $suggestion
 * @property-read string|null $urlImage
 */
class SuggestionOption extends Model
{
    use DeletesStoredImages;

    protected $table = 'suggestion_options';

    protected $fillable = ['suggestion_id', 'value', 'is_correct', 'image_path', 'order'];

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
     *
     * @return BelongsTo<Suggestion, $this>
     */
    public function suggestion(): BelongsTo
    {
        return $this->belongsTo(Suggestion::class, 'suggestion_id', 'id');
    }

    /**
     * Devuelve la url hacia la imagen si tuviera.
     */
    public function getUrlImageAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
