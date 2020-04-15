<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TelegramChatResource extends JsonResource
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
            'title',
            'type',
            'first_name',
            'last_name',
            'username',
            'action',
            'phone',
            'invite_link',
            'app_invite_link',
            'name',
        ]);

        return $resource;
    }
}
