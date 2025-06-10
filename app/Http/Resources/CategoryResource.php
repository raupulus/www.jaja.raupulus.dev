<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'urlImage' => $this->image ? $this->urlImage : null,
            //'contents' => ContentResource::collection($this->whenLoaded('contents')),
        ];
    }
}
