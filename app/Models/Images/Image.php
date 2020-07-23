<?php

namespace App\Models\Images;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Image
 * @package App\Models\Images
 */
class Image extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'path',
        'imageable_id',
        'imageable_type',
    ];

    /**
     * @return MorphTo
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
