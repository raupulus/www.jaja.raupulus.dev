<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredImages;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Group
 *
 * Representa un grupo demográfico o público objetivo (ej. Adultos, General).
 */
class Group extends Model
{
    use DeletesStoredImages;

    protected $table = 'groups';

    protected $fillable = ['type_id', 'title', 'slug', 'image', 'description'];

    /**
     * Atributos de imagen gestionados en Storage::disk('public').
     *
     * @return array<string, string|null>
     */
    protected function storedImageAttributes(): array
    {
        return ['image' => 'images/default/group.webp'];
    }

    /**
     * Relación con el tipo del grupo
     *
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    /**
     * Relación con los contenidos del grupo.
     *
     * @return HasMany
     */
    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'group_id', 'id');
    }

    /**
     * Obtiene el último contenido subido para este grupo
     *
     * @return \App\Models\Content|null
     */
    public function getLastContentAttribute()
    {
        return $this->contents()->latest('created_at')->first();
    }

    /**
     * Obtiene un mensaje sobre la última actualización de contenido
     *
     * @return string
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
            $lastContent = $this->last_content;
            if (!$lastContent) {
                return '¡Sé el primero en subir contenido!';
            }
            $lastContentDate = Carbon::parse($lastContent->created_at);
        }

        if (!$lastContentDate) {
            return '¡Sé el primero en subir contenido!';
        }

        $now = Carbon::now();

        ## Si es contenido del futuro, tratar como muy reciente
        if ($lastContentDate->isFuture()) {
            return 'Hace unos momentos';
        }

        $diff = $lastContentDate->diffForHumans($now, true);

        ##Si es más de 5 días, muestro mensaje especial
        if ($now->diffInDays($lastContentDate) > 5) {
            return 'Hace demasiado tiempo, ¡sube el tuyo!';
        }

        return "Hace {$diff}";
    }


    /**
     * Devuelve la url hacia la imagen.
     *
     * @return string
     */
    public function getUrlImageAttribute(): string
    {
        if (!$this->image || ($this->image === 'images/default/group.webp')) {
            return asset('images/default/group.webp');
        }

        return asset('storage/' . $this->image);
    }

    /**
     * Scope para excluir grupos reservados/no aptos de los listados públicos.
     */
    public function scopePublicSafe(Builder $query): Builder
    {
        $excludedIds = Content::excludedPublicGroupIds();

        if ($excludedIds !== []) {
            return $query->whereNotIn($query->qualifyColumn('id'), $excludedIds);
        }

        return $query;
    }

    /**
     * Scope para filtrar grupos que tienen contenidos disponibles públicamente.
     */
    public function scopeHasAvailableContent(Builder $query): Builder
    {
        return $query->whereHas('contents', function (Builder $q): void {
            $q->publicSafe();
        });
    }
}
