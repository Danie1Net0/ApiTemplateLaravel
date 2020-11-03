<?php

namespace App\Http\Resources\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MessageResponseResource
 * @package App\Http\Resources\Shared
 */
class MessageResponseResource extends JsonResource
{
    /**
     * MessageResponseResource constructor.
     * @param $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
        JsonResource::withoutWrapping();
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'message' => $this->resource
        ];
    }
}
