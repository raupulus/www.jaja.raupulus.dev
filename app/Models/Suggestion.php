<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Suggestion extends Model
{
    use softDeletes;

    protected $table = 'suggestions';

    protected $fillable = ['type_id', 'nick', 'title', 'content', 'image_path', 'ip_address', 'user_agent', 'approved_at',  'group_id',];


    /**
     * Relación con el tipo de contenido.
     *
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    /**
     * Relación con la categoría del contenido.
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'suggestion_categories', 'suggestion_id', 'category_id');
    }

    /**
     * Relación con el grupo al que pertenece el contenido.
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    /**
     * Relación con las opciones asociadas al contenido.
     *
     * @return HasMany
     */
    public function options(): HasMany
    {
        return $this->hasMany(SuggestionOption::class, 'suggestion_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($suggestion) {
            ## Aseguro que siempre tenga la categoría General si no hay categorías
            if ($suggestion->categories()->count() === 0) {
                $suggestion->categories()->attach(1);
            }
        });

        ## Evento ejecutado antes de hacer un softDelete
        static::deleting(function ($suggestion) {
            if ($suggestion->image_path) {
                $suggestion->deleteImage();
            }
        });

        ## Evento ejecutado antes de hacer un forceDelete
        static::forceDeleting(function ($suggestion) {
            if ($suggestion->image_path) {
                $suggestion->deleteImage();
            }
        });
    }


    /**
     * Devuelve la url hacia la imagen o null si no tuviera.
     *
     * @return string|null
     */
    public function getUrlImageAttribute(): string|null
    {
        return $this->image_path ? asset('storage' . $this->image_path) : null;
    }

    /**
     * Aprobar sugerencias de contenido.
     *
     * @return bool
     */
    public function approve(): bool
    {
        if (!$this->group_id) {
            Notification::make()
                ->title('Grupo no seleccionado')
                ->body('Debes seleccionar un grupo antes de aprobar esta sugerencia.')
                ->danger()
                ->send();
        }

        if (!$this->categories()->count()) {
            Notification::make()
                ->title('Categoría/s no seleccionada/s')
                ->body('Debes seleccionar al menos una categoría antes de aprobar esta sugerencia.')
                ->danger()
                ->send();
        }

        if (!$this->group_id || !$this->categories()->count()) {
            return false;
        }

        $content = Content::create([
            'user_id' => auth()->id(),
            'group_id' => $this->group_id,
            'title' => $this->title,
            'content' => $this->content,
            'uploaded_by' => $this->nick ?? null,
        ]);

        $categories = $this->categories()->pluck('categories.id')->toArray();
        $content->categories()->attach($categories);

        ## Copio las opciones solo si es tipo quiz
        if ($this->type->slug === 'quiz') {
            $suggestionOptions = $this->options()->orderBy('order')->get();

            foreach ($suggestionOptions as $suggestionOption) {
                $contentOption = ContentOption::create([
                    'content_id' => $content->id,
                    'value' => $suggestionOption->value,
                    'is_correct' => $suggestionOption->is_correct,
                    'order' => $suggestionOption->order,
                ]);

                ## Copio la imagen de la opción si existe
                if ($suggestionOption->image_path) {
                    $optionFilename = basename($suggestionOption->image_path);
                    $contentOptionImagePath = 'content-option-images/' . $contentOption->id . '_' . $optionFilename;

                    ## Por si no existe el directorio
                    Storage::disk('public')->makeDirectory('content-option-images');

                    if (Storage::disk('public')->exists($suggestionOption->image_path)) {
                        Storage::disk('public')->copy($suggestionOption->image_path, $contentOptionImagePath);

                        $contentOption->image_path = $contentOptionImagePath;
                        $contentOption->save();

                        ## Elimino imagen original de sugerencia
                        Storage::disk('public')->delete($suggestionOption->image_path);
                        $suggestionOption->update(['image_path' => null]);
                    }
                }
            }
        }

        if ($this->image_path) {
            $filename = basename($this->image_path);
            $contentImagePath = 'content-images/' . $content->id . '_' . $filename;

            ## Asegura de que el directorio existe
            Storage::disk('public')->makeDirectory('content-images');

            if (Storage::disk('public')->exists($this->image_path)) {
                Storage::disk('public')->copy($this->image_path, $contentImagePath);

                $content->image = $contentImagePath;
                $content->save();

                Storage::disk('public')->delete($this->image_path);
            }
        }

        $this->image_path = null;
        $this->approved_at = now();
        $this->save();
        $this->runSoftDelete();

        return true; // Cambiado a true para indicar éxito
    }

    /**
     * Elimina la imagen del almacenamiento.
     */
    public function deleteImage(): bool
    {
        if (!$this->image_path) {
            return false;
        }

        // Si la imagen existe en el almacenamiento, la eliminamos
        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->delete($this->image_path);
        }

        return false;
    }
}
