<?php

namespace App\Http\Resources\Telephones;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TelephoneResource
 * @package App\Http\Resources\Telephones
 */
class TelephoneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'number' => $this->number,
            'type' => $this->type
        ];
    }
}
