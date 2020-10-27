<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Telephone
 * @package App\Models\Shared
 */
class Telephone extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'number',
        'type',
    ];

    /**
     * @var string[]
     */
    protected $visible = [
        'number',
        'type',
    ];

    /**
     * @return MorphTo
     */
    public function telephonable(): MorphTo
    {
        return $this->morphTo();
    }
}
