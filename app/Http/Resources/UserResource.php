<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->only([
            'id',
            'first_name',
            'last_name',
            'patronymic',
            'short_name',
            'full_name',
            'patronymic',
            'user_category_id',
            'role',
            'email',
        ]);

        return $resource;
    }
}
