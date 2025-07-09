<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'urlImage' => $this->image ? $this->urlImage : null,
            'order' => $this->order,
            'is_correct' => $this->is_correct,
        ];
    }
}
