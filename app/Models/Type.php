<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredImages;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Database\Factories\TypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Type
 *
 * Representa el tipo o formato estructural del contenido (ej. Chiste, Quiz, Adivinanza).
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $last_content_date
 * @property-read Content|null $last_content
 * @property-read string $last_update_message
 * @property-read string $urlImage
 */
class Type extends Model
{
    use DeletesStoredImages;

    /** @use HasFactory<TypeFactory> */
    use HasFactory;

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
     *
     * @return HasMany<Group, $this>
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'type_id', 'id');
    }

    /**
     * Contenidos asociados al tipo a través de grupos
     *
     * @return HasManyThrough<Content, Group, $this>
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
     *
     * @return HasManyThrough<Content, Group, $this>
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
        $lastContent = $this->contents()->latest('created_at')->first();

        return $lastContent instanceof Content ? $lastContent : null;
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

        $now = Carbon::now();

        // Si es contenido del futuro, tratar como muy reciente
        if ($lastContentDate->isFuture()) {
            return 'Hace unos momentos';
        }

        // Usar diffForHumans() de Carbon que maneja mejor los casos edge
        $diff = $lastContentDate->diffForHumans($now, CarbonInterface::DIFF_ABSOLUTE);

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
