<?php

namespace App\Http\Resources;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Type */
class TypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'urlImage' => $this->image ? $this->urlImage : null,
            // 'groups' => GroupResource::collection($this->whenLoaded('groups')),
        ];
    }
}
