<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use softDeletes;

    protected $table = 'contents';

    protected $fillable = ['user_id', 'group_id', 'title', 'content', 'image_id'];

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
}
