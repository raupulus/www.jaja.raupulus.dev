<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Content extends Model
{
    use softDeletes;

    protected $table = 'contents';

    protected $fillable = ['user_id', 'group_id', 'title', 'content', 'image', 'uploaded_by', 'last_social_published', 'is_adult', 'is_ai'];

    protected function casts(): array
    {
        return [
            'last_social_published' => 'datetime',
        ];
    }

    /**
     * El boot del modelo registra los eventos
     */
    protected static function boot()
    {
        parent::boot();

        ## Evento que se ejecuta después de crear un contenido
        static::created(function ($content) {
            if ($content->categories()->count() === 0) {
                $content->categories()->attach(1); // ID 1 = General
            }
        });

        ## Evento que se ejecuta después de actualizar un contenido
        static::updated(function ($content) {
            if ($content->categories()->count() === 0) {
                $content->categories()->attach(1); // ID 1 = General
            }
        });


        ## Evento que se ejecuta antes de crear un nuevo registro
        static::creating(function ($content) {
            ## Si no se ha asignado un user_id, asignar el del usuario logueado
            if (empty($content->user_id) && auth()->check()) {
                $content->user_id = auth()->id();
            }
        });

        ## Evento que se ejecuta antes de hacer un forceDelete (eliminación definitiva)
        static::forceDeleting(function ($content) {
            ## Si el contenido tiene una imagen y no es la imagen por defecto
            if ($content->image && $content->image !== 'images/default/content.webp') {
                $imagePath = storage_path('app/public/' . $content->image);

                ## Verifico si el archivo existe antes de eliminarlo
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        });

    }

    /**
     * Reportes de este contenido
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
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
     * Relación con las categorías del contenido.
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'content_categories', 'content_id', 'category_id');
    }

    /**
     * Relación con las opciones asociadas al contenido.
     *
     * @return HasMany
     */
    public function options(): HasMany
    {
        return $this->hasMany(ContentOption::class, 'content_id', 'id');
    }

    /**
     * Scope para filtrar contenidos por grupo
     */
    public function scopeByGroup(Builder $query, Group $group): Builder
    {
        return $query->where('group_id', $group->id);
    }

    /**
     * Scope para filtrar contenidos por tipo
     */
    public function scopeByType(Builder $query, Type $type): Builder
    {
        return $query->whereHas('group', function ($q) use ($type) {
            $q->where('type_id', $type->id);
        });
    }

    /**
     * Scope para filtrar contenidos por categoría
     */
    public function scopeByCategory(Builder $query, Category $category): Builder
    {
        return $query->whereHas('categories', function ($q) use ($category) {
            $q->where('categories.id', $category->id);
        });
    }

    /**
     * Scope para filtrar contenidos por grupo Y categoría
     */
    public function scopeByGroupAndCategory(Builder $query, Group $group, Category $category): Builder
    {
        return $query->where('group_id', $group->id)
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.id', $category->id);
            });
    }

    /**
     * Scope para filtrar contenidos por tipo Y categoría
     */
    public function scopeByTypeAndCategory(Builder $query, Type $type, Category $category): Builder
    {
        return $query->whereHas('group', function ($q) use ($type) {
            $q->where('type_id', $type->id);
        })->whereHas('categories', function ($q) use ($category) {
            $q->where('categories.id', $category->id);
        });
    }

    /**
     * Scope para filtrar contenidos por grupo, tipo Y categoría
     */
    public function scopeByGroupTypeAndCategory(Builder $query, Group $group, Type $type, Category $category): Builder
    {
        return $query->where('group_id', $group->id)
            ->whereHas('group', function ($q) use ($type) {
                $q->where('type_id', $type->id);
            })
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.id', $category->id);
            });
    }

    /**
     * Scope para obtener contenido aleatorio
     */
    public function scopeRandom(Builder $query): Builder
    {
        return $query->inRandomOrder();
    }

    /**
     * Devuelve el contenido en HTML con saltos de línea convertidos a <br />
     *
     * @return string
     */
    public function getFormattedHtmlContentAttribute(): string
    {
        return nl2br(e($this->content));
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
