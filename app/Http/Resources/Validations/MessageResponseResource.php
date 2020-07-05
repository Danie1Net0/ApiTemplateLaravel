<?php

namespace App\Http\Resources\Validations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MessageResponseResource
 * @package App\Http\Resources\Validations
 */
class MessageResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource;
    }
}
