<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * Class Image
 * @package App\Models\Shared
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
     * @var string[]
     */
    protected $visible = [
        'id',
        'path',
    ];

    /**
     * @return MorphTo
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Accessor
     *
     * @return string
     */
    public function getPathAttribute($value): string
    {
    	return url(Storage::url($value));
    }
}
