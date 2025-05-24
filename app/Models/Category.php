<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use softDeletes;

    protected $table = 'categories';
    protected $fillable = ['title', 'description', 'image', ];

    /**
     * Devuelve la url hacia la imagen.
     *
     * @return string
     */
    public function getUrlImageAttribute(): string
    {
        if (!$this->image || ($this->image === 'images/default/category.webp')) {
            return asset('images/default/category.webp');
        }

        return asset('storage/' . $this->image);
    }
}
