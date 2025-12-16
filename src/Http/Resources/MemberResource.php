<?php

namespace DET\Members\Http\Resources;

use DET\Members\Models\Member;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Member
 */
class MemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => (bool) $this->is_active,
            'last_login_at' => $this->last_login_at,
            'created_at' => $this->created_at->toIso8601String(),
            // Include Profile if loaded
            'profile' => new MemberProfileResource($this->whenLoaded('profile')),
            // Include Roles if Admin
            'roles' => $this->when($this->resource->relationLoaded('roles'), function () {
                return $this->roles->pluck('name');
            }),
        ];
    }
}
