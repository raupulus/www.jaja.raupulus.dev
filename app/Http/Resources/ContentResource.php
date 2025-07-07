<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'urlImage' => $this->image ? $this->urlImage : null,
            'uploader' => $this->uploader,
            //'group' => new GroupResource($this->whenLoaded('group')),
            //'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            //'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
