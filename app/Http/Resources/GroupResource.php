<?php

namespace App\Http\Resources;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Group */
class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'urlImage' => $this->image ? $this->urlImage : null,
            // 'type' => new TypeResource($this->whenLoaded('type')),
            // 'contents' => ContentResource::collection($this->whenLoaded('contents')),
        ];
    }
}
