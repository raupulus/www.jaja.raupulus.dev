<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use softDeletes;

    protected $table = 'contents';

    protected $fillable = ['user_id', 'group_id', 'title', 'content', 'image'];

    /**
     * El boot del modelo registra los eventos
     */
    protected static function boot()
    {
        parent::boot();

        // Evento que se ejecuta antes de crear un nuevo registro
        static::creating(function ($content) {
            // Si no se ha asignado un user_id, asignar el del usuario logueado
            if (empty($content->user_id) && auth()->check()) {
                $content->user_id = auth()->id();
            }
        });
    }


    /**
     * Relación con el Propietario del contenido.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relación con el Grupo al que pertenece el contenido.
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    /**
     * Devuelve el nick del usuario que ha subido el contenido
     *
     * @return string
     */
    public function getUploaderAttribute(): string
    {
        return '@' . ($this->uploaded_by ?? $this->user?->nick ?? 'SinAsignar');
    }

    /**
     * Devuelve la url hacia la imagen.
     *
     * @return string
     */
    public function getUrlImageAttribute(): string
    {
        if (!$this->image || ($this->image === 'images/default/content.webp')) {
            return asset('images/default/content.webp');
        }

        return asset('storage/' . $this->image);
    }
}
