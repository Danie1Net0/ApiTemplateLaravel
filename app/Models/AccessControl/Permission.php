<?php

namespace App\Models\AccessControl;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Class Permission
 * @package App\Models\AccessControl
 */
class Permission extends SpatiePermission
{
    /**
     * @var string[]
     */
    protected $visible = [
        'id',
        'name',
    ];
}
