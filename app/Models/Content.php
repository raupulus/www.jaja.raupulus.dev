<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredImages;
use Carbon\Carbon;
use Database\Factories\ContentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Content
 *
 * Representa el contenido principal aprobado (chistes, adivinanzas, quiz) en la plataforma.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $group_id
 * @property string $title
 * @property string $content
 * @property string|null $image
 * @property string|null $uploaded_by
 * @property Carbon|null $last_social_published
 * @property bool $is_adult
 * @property bool $is_ai
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $user
 * @property-read Group|null $group
 * @property-read string $uploader
 * @property-read string $urlImage
 * @property-read string $formattedHtmlContent
 */
class Content extends Model
{
    use DeletesStoredImages;

    /** @use HasFactory<ContentFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * IDs de grupos excluidos por defecto de listados públicos y difusión social.
     *
     * @var list<int>
     */
    public const EXCLUDED_PUBLIC_GROUP_IDS = [4, 14];

    protected $table = 'contents';

    protected $fillable = ['user_id', 'group_id', 'title', 'content', 'image', 'uploaded_by', 'last_social_published', 'is_adult', 'is_ai'];

    /**
     * Atributos de imagen gestionados en Storage::disk('public').
     *
     * @return array<string, string|null>
     */
    protected function storedImageAttributes(): array
    {
        return ['image' => 'images/default/content.webp'];
    }

    /**
     * Método casts.
     */
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

        // # Evento que se ejecuta después de crear un contenido
        static::created(function ($content) {
            if ($content->categories()->count() === 0) {
                $content->categories()->attach(1); // ID 1 = General
            }
        });

        // # Evento que se ejecuta después de actualizar un contenido
        static::updated(function ($content) {
            if ($content->categories()->count() === 0) {
                $content->categories()->attach(1); // ID 1 = General
            }
        });

        // # Evento que se ejecuta antes de crear un nuevo registro
        static::creating(function ($content) {
            // # Si no se ha asignado un user_id, asignar el del usuario logueado
            if (empty($content->user_id) && auth()->check()) {
                $content->user_id = auth()->id();
            }
        });

    }

    /**
     * Reportes de este contenido
     *
     * @return MorphMany<Report, $this>
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    /**
     * Relación con el Propietario del contenido.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relación con el Grupo al que pertenece el contenido.
     *
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    /**
     * Relación con las categorías del contenido.
     *
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'content_categories', 'content_id', 'category_id');
    }

    /**
     * Relación con las opciones asociadas al contenido.
     *
     * @return HasMany<ContentOption, $this>
     */
    public function options(): HasMany
    {
        return $this->hasMany(ContentOption::class, 'content_id', 'id');
    }

    /**
     * Scope para filtrar contenidos por grupo
     *
     * @param  Builder<Content>  $query
     * @return Builder<Content>
     */
    public function scopeByGroup(Builder $query, Group $group): Builder
    {
        return $query->where('group_id', $group->id);
    }

    /**
     * Scope para filtrar contenidos por tipo
     *
     * @param  Builder<Content>  $query
     * @return Builder<Content>
     */
    public function scopeByType(Builder $query, Type $type): Builder
    {
        return $query->whereHas('group', function ($q) use ($type) {
            $q->where('type_id', $type->id);
        });
    }

    /**
     * Scope para filtrar contenidos por categoría
     *
     * @param  Builder<Content>  $query
     * @return Builder<Content>
     */
    public function scopeByCategory(Builder $query, Category $category): Builder
    {
        return $query->whereHas('categories', function ($q) use ($category) {
            $q->where('categories.id', $category->id);
        });
    }

    /**
     * Scope para filtrar contenidos por grupo Y categoría
     *
     * @param  Builder<Content>  $query
     * @return Builder<Content>
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
     *
     * @param  Builder<Content>  $query
     * @return Builder<Content>
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
     *
     * @param  Builder<Content>  $query
     * @return Builder<Content>
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
     * Devuelve los IDs de grupos excluidos de las rutas y API públicas.
     *
     * @return list<int>
     */
    public static function excludedPublicGroupIds(): array
    {
        $configured = config('content.excluded_public_group_ids', self::EXCLUDED_PUBLIC_GROUP_IDS);

        if (! is_array($configured)) {
            return self::EXCLUDED_PUBLIC_GROUP_IDS;
        }

        return array_values(array_filter(
            array_map('intval', $configured),
            static fn (int $id): bool => $id > 0
        ));
    }

    /**
     * Scope para filtrar contenido apto para listados públicos, API pública y redes sociales.
     *
     * @param  Builder<Content>  $query
     * @return Builder<Content>
     */
    public function scopePublicSafe(Builder $query): Builder
    {
        $excludedIds = self::excludedPublicGroupIds();

        if ($excludedIds !== []) {
            $query->whereNotIn($query->qualifyColumn('group_id'), $excludedIds);
        }

        return $query->where($query->qualifyColumn('is_adult'), false);
    }

    /**
     * Indica si este registro concreto es apto para listados públicos y difusión social.
     */
    public function isPublicSafe(): bool
    {
        return ! in_array((int) $this->group_id, self::excludedPublicGroupIds(), true) && ! $this->is_adult;
    }

    /**
     * Scope para obtener contenido aleatorio
     *
     * @param  Builder<Content>  $query
     * @return Builder<Content>
     */
    public function scopeRandom(Builder $query): Builder
    {
        return $query->inRandomOrder();
    }

    /**
     * Devuelve el contenido en HTML con saltos de línea convertidos a <br />
     */
    public function getFormattedHtmlContentAttribute(): string
    {
        return nl2br(e($this->content));
    }

    /**
     * Devuelve el nick del usuario que ha subido el contenido
     */
    public function getUploaderAttribute(): string
    {
        return '@'.($this->uploaded_by ?? $this->user->nick ?? 'SinAsignar');
    }

    /**
     * Devuelve la url hacia la imagen.
     */
    public function getUrlImageAttribute(): string
    {
        if (! $this->image || ($this->image === 'images/default/content.webp')) {
            return asset('images/default/content.webp');
        }

        return asset('storage/'.$this->image);
    }
}
