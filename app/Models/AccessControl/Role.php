<?php

namespace App\Models\AccessControl;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Traits\HasPermissions;

/**
 * Class Role
 * @package App\Models\AccessControl
 */
class Role extends SpatieRole
{
    use HasPermissions, Uuid;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $visible = [
        'id',
        'name',
        'permissions',
    ];
}
