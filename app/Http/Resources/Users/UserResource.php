<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\AccessControl\RoleResource;
use App\Http\Resources\Telephones\TelephoneResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Class UserResource
 * @package App\Http\Resources\Users
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->when($this->id, $this->id),
            'name' => $this->when($this->name, $this->name),
            'email' => $this->when($this->email, $this->email),
            'path_image' => $this->when($this->path_image, url(Storage::url($this->path_image ?? 'public/user_images/default.png'))),
            'is_active' => $this->when($this->is_active, $this->is_active ?? false),
            'telephones' => TelephoneResource::collection($this->telephones),
            'roles' => RoleResource::collection($this->roles),
        ];
    }
}
