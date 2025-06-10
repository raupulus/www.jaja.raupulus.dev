<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'nick' => $this->nick,
            'urlImage' => $this->urlImage,

            ## Si es el usuario logueado incluyo información más sensible
            $this->mergeWhen($request->user()?->id === $this->id, [
                'email' => $this->email,
                'email_verified_at' => $this->email_verified_at,
            ]),
        ];
    }
}
