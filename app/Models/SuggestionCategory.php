<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo SuggestionCategory
 *
 * Tabla pivot que relaciona sugerencias pendientes con categorías.
 */
class SuggestionCategory extends Model
{

    /**
     * Devuelve todas las sugerencias asociadas a una categoría.
     *
     * @return HasMany
     */
    public function suggestions(): HasMany
    {
        return $this->hasMany(Suggestion::class);
    }
}
