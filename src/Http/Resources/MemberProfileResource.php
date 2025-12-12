<?php

namespace Det\Members\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name . ' ' . $this->last_name, // Accessor logic
            'initials' => substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1),
            'avatar' => $this->avatar,
            'age' => $this->date_of_birth ? $this->date_of_birth->age : null,
            'settings' => $this->settings,
            'preferences' => $this->preferences,
        ];
    }
}