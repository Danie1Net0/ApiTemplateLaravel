<?php

namespace App\Http\Resources\Users;

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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'path_image' => url(Storage::url($this->path_image ?? 'public/user_images/default.png')),
            'is_active' => $this->is_active ?? false,
            'telephones' => TelephoneResource::collection($this->telephones)
        ];
    }
}
