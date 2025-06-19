<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';

    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'image', 'keywords', 'status'];

    /**
     * Devuelve el enlace a la página actual.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return route('page.show', $this->slug);
    }

    /**
     * Devuelve la url hacia la imagen principal de la página.
     *
     * @return string|null
     */
    public function getUrlImageAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    //TODO: Adaptar cuando implemente generar thumbnails
    public function getUrlImageThumbnailAttribute(): ?string
    {
        return $this->urlImage;
    }

    /**
     * Parsea el contenido en markdown y lo devuelve en formato HTML
     *
     * @return string
     */
    public function getHtmlContent(): string
    {
        $parsedown = new \Parsedown();
        $parsedown->setSafeMode(true);

        return $parsedown->text($this->content);
    }

}
