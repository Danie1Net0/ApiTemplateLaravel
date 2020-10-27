<?php

namespace App\Models\AccessControl;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Class Role
 * @package App\Models\AccessControl
 */
class Role extends SpatieRole
{
    /**
     * @var string[]
     */
    protected $visible = [
        'name',
    ];
}
