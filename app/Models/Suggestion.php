<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredImages;
use App\Services\CacheInvalidationService;
use Carbon\Carbon;
use Database\Factories\SuggestionFactory;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Modelo Suggestion
 *
 * Representa una sugerencia de contenido aportada por un usuario y pendiente de moderación.
 *
 * @property int $id
 * @property int|null $type_id
 * @property int|null $group_id
 * @property string|null $nick
 * @property string $title
 * @property string $content
 * @property string|null $image_path
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon|null $approved_at
 * @property bool $is_adult
 * @property bool $is_ai
 * @property Carbon|null $deleted_at
 * @property-read Type|null $type
 * @property-read Group|null $group
 * @property-read string|null $urlImage
 */
class Suggestion extends Model
{
    use DeletesStoredImages;

    /** @use HasFactory<SuggestionFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $table = 'suggestions';

    protected $fillable = ['type_id', 'nick', 'title', 'content', 'image_path', 'ip_address', 'user_agent', 'approved_at',  'group_id', 'is_adult', 'is_ai'];

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
     * Relación con el tipo de contenido.
     *
     * @return BelongsTo<Type, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    /**
     * Relación con la categoría del contenido.
     *
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'suggestion_categories', 'suggestion_id', 'category_id');
    }

    /**
     * Relación con el grupo al que pertenece el contenido.
     *
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    /**
     * Relación con las opciones asociadas al contenido.
     *
     * @return HasMany<SuggestionOption, $this>
     */
    public function options(): HasMany
    {
        return $this->hasMany(SuggestionOption::class, 'suggestion_id', 'id');
    }

    /**
     * Método boot.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($suggestion) {
            // # Aseguro que siempre tenga la categoría General si no hay categorías
            if ($suggestion->categories()->count() === 0) {
                $suggestion->categories()->attach(1);
            }
        });

        // # Evento ejecutado antes de hacer un softDelete
        static::deleting(function ($suggestion) {
            if ($suggestion->image_path) {
                $suggestion->deleteImage();
            }
        });

        // # Evento ejecutado antes de hacer un forceDelete
        static::forceDeleting(function ($suggestion) {
            if ($suggestion->image_path) {
                $suggestion->deleteImage();
            }
        });
    }

    /**
     * Devuelve la url hacia la imagen o null si no tuviera.
     */
    public function getUrlImageAttribute(): ?string
    {
        return $this->image_path ? asset('storage'.$this->image_path) : null;
    }

    /**
     * Aprobar sugerencias de contenido de forma atómica.
     */
    public function approve(): bool
    {
        $this->loadMissing('type');

        if (! $this->group_id) {
            Notification::make()
                ->title('Grupo no seleccionado')
                ->body('Debes seleccionar un grupo antes de aprobar esta sugerencia.')
                ->danger()
                ->send();
        }

        $categories = $this->categories()->pluck('categories.id')->toArray();

        if (empty($categories)) {
            Notification::make()
                ->title('Categoría/s no seleccionada/s')
                ->body('Debes seleccionar al menos una categoría antes de aprobar esta sugerencia.')
                ->danger()
                ->send();
        }

        if (! $this->group_id || empty($categories)) {
            return false;
        }

        $copiedFiles = [];
        $sourceFilesToDelete = [];

        try {
            DB::transaction(function () use ($categories, &$copiedFiles, &$sourceFilesToDelete): void {
                $userExist = $this->nick ? User::where('nick', $this->nick)->first() : null;

                $content = Content::create([
                    'user_id' => $userExist->id ?? auth()->id(),
                    'group_id' => $this->group_id,
                    'title' => $this->title,
                    'content' => $this->content,
                    'uploaded_by' => $userExist ? null : ($this->nick ?? null),
                    'is_adult' => $this->is_adult ?? false,
                    'is_ai' => $this->is_ai ?? false,
                ]);

                $content->categories()->syncWithoutDetaching($categories);

                // # Copio las opciones solo si es tipo quiz
                if ($this->type?->slug === 'quiz') {
                    $suggestionOptions = $this->options()->orderBy('order')->get();

                    foreach ($suggestionOptions as $suggestionOption) {
                        $contentOption = ContentOption::create([
                            'content_id' => $content->id,
                            'value' => $suggestionOption->value,
                            'is_correct' => $suggestionOption->is_correct,
                            'order' => $suggestionOption->order,
                        ]);

                        // # Copio la imagen de la opción si existe
                        if ($suggestionOption->image_path && Storage::disk('public')->exists($suggestionOption->image_path)) {
                            $optionFilename = basename($suggestionOption->image_path);
                            $contentOptionImagePath = 'content-option-images/'.$contentOption->id.'_'.$optionFilename;

                            Storage::disk('public')->makeDirectory('content-option-images');
                            Storage::disk('public')->copy($suggestionOption->image_path, $contentOptionImagePath);

                            $copiedFiles[] = $contentOptionImagePath;
                            $sourceFilesToDelete[] = $suggestionOption->image_path;

                            $contentOption->image_path = $contentOptionImagePath;
                            $contentOption->save();

                            $suggestionOption->image_path = null;
                            $suggestionOption->saveQuietly();
                        }
                    }
                }

                if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
                    $filename = basename($this->image_path);
                    $contentImagePath = 'content-images/'.$content->id.'_'.$filename;

                    Storage::disk('public')->makeDirectory('content-images');
                    Storage::disk('public')->copy($this->image_path, $contentImagePath);

                    $copiedFiles[] = $contentImagePath;
                    $sourceFilesToDelete[] = $this->image_path;

                    $content->image = $contentImagePath;
                    $content->save();
                }

                $this->image_path = null;
                $this->approved_at = now();
                $this->saveQuietly();
                $this->runSoftDelete();
            });
        } catch (\Throwable $e) {
            foreach ($copiedFiles as $copiedFile) {
                if (Storage::disk('public')->exists($copiedFile)) {
                    Storage::disk('public')->delete($copiedFile);
                }
            }

            throw $e;
        }

        foreach ($sourceFilesToDelete as $sourceFile) {
            if (Storage::disk('public')->exists($sourceFile)) {
                Storage::disk('public')->delete($sourceFile);
            }
        }

        CacheInvalidationService::forSuggestion();

        return true;
    }

    /**
     * Elimina la imagen del almacenamiento.
     */
    public function deleteImage(): bool
    {
        if (! $this->image_path) {
            return false;
        }

        // Si la imagen existe en el almacenamiento, la eliminamos
        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->delete($this->image_path);
        }

        return false;
    }
}
