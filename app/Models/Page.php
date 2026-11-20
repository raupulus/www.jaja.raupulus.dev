<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredImages;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Page
 *
 * Representa una página estática o legal del mini-CMS integrado.
 */
class Page extends Model
{
    use DeletesStoredImages;

    protected $table = 'pages';

    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'image', 'keywords', 'status'];

    /**
     * Atributos de imagen gestionados en Storage::disk('public').
     *
     * @return array<string, string|null>
     */
    protected function storedImageAttributes(): array
    {
        return ['image' => null];
    }

    /**
     * Devuelve el enlace a la página actual.
     */
    public function getUrlAttribute(): string
    {
        return route('page.show', $this->slug);
    }

    /**
     * Devuelve la url hacia la imagen principal de la página.
     */
    public function getUrlImageAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }

    // TODO: Adaptar cuando implemente generar thumbnails
    /**
     * Método getUrlImageThumbnailAttribute.
     */
    public function getUrlImageThumbnailAttribute(): ?string
    {
        return $this->urlImage;
    }

    /**
     * Parsea el contenido en markdown y lo devuelve en formato HTML
     */
    public function getHtmlContent(): string
    {
        $parsedown = new \Parsedown;
        $parsedown->setSafeMode(true);

        return $parsedown->text($this->content);
    }
}
