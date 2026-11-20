<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredImages;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Type
 *
 * Representa el tipo o formato estructural del contenido (ej. Chiste, Quiz, Adivinanza).
 */
class Type extends Model
{
    use DeletesStoredImages;
    use SoftDeletes;

    protected $table = 'types';

    protected $fillable = ['name', 'slug', 'description', 'image'];

    /**
     * Atributos de imagen gestionados en Storage::disk('public').
     *
     * @return array<string, string|null>
     */
    protected function storedImageAttributes(): array
    {
        return ['image' => 'images/default/type.webp'];
    }

    /**
     * Grupos asociados al tipo.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'type_id', 'id');
    }

    /**
     * Contenidos asociados al tipo a través de grupos
     */
    public function contents(): HasManyThrough
    {
        return $this->hasManyThrough(
            Content::class,
            Group::class,
            'type_id',  // Foreign key en groups que apunta a types
            'group_id', // Foreign key en contents que apunta a groups
            'id',       // Local key en types
            'id'        // Local key en groups
        );
    }

    /**
     * Relación con las categorías a través de contenidos
     */
    public function categories(): HasManyThrough
    {
        return $this->hasManyThrough(
            Content::class,
            Group::class,
            'type_id',  // Foreign key en groups que apunta a types
            'group_id', // Foreign key en contents que apunta a groups
            'id',       // Local key en types
            'id'        // Local key en groups
        );
    }

    /**
     * Obtiene el último contenido subido para este tipo
     */
    public function getLastContentAttribute(): ?Content
    {
        return $this->contents()->latest('created_at')->first();
    }

    /**
     * Obtiene un mensaje sobre la última actualización de contenido
     */
    public function getLastUpdateMessageAttribute(): string
    {
        if (array_key_exists('last_content_date', $this->attributes)) {
            if (! $this->attributes['last_content_date']) {
                return '¡Sé el primero en subir contenido!';
            }
            $lastContentDate = Carbon::parse($this->attributes['last_content_date']);
        } elseif (isset($this->last_content_date)) {
            $lastContentDate = Carbon::parse($this->last_content_date);
        } else {
            // Fallback al método original
            $lastContent = $this->last_content;
            if (! $lastContent) {
                return '¡Sé el primero en subir contenido!';
            }
            $lastContentDate = Carbon::parse($lastContent->created_at);
        }

        if (! $lastContentDate) {
            return '¡Sé el primero en subir contenido!';
        }

        $now = Carbon::now();

        // Si es contenido del futuro, tratar como muy reciente
        if ($lastContentDate->isFuture()) {
            return 'Hace unos momentos';
        }

        // Usar diffForHumans() de Carbon que maneja mejor los casos edge
        $diff = $lastContentDate->diffForHumans($now, true);

        // Si es más de 5 días, mostrar mensaje especial
        if ($now->diffInDays($lastContentDate) > 5) {
            return 'Hace demasiado tiempo, ¡sube el tuyo!';
        }

        return "Hace {$diff}";
    }

    /**
     * Devuelve la url hacia la imagen.
     */
    public function getUrlImageAttribute(): string
    {
        if (! $this->image || ($this->image === 'images/default/type.webp')) {
            return asset('images/default/type.webp');
        }

        return asset('storage/'.$this->image);
    }
}
