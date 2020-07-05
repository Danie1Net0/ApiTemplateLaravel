<?php

namespace App\Models\Telephones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Telephone
 * @package App\Models\Telephones
 */
class Telephone extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'number',
        'type'
    ];

    /**
     * @return MorphTo
     */
    public function telephonable(): MorphTo
    {
        return $this->morphTo();
    }
}
