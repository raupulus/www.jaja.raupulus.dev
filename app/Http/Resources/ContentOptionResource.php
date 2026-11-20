<?php

namespace App\Http\Resources;

use App\Models\ContentOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ContentOption */
class ContentOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'urlImage' => $this->image_path ? $this->urlImage : null,
            'order' => $this->order,
            'is_correct' => $this->is_correct,
        ];
    }
}
