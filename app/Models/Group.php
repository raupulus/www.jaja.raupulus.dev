<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $table = 'groups';

    protected $fillable = ['type_id', 'title', 'slug', 'image', 'description'];

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
        ## Si se cargó desde el controlador con optimización
        if (isset($this->last_content_date)) {
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
}
