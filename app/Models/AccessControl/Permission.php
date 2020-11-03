<?php

namespace App\Models\AccessControl;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Permission
 * @package App\Models\AccessControl
 */
class Permission extends SpatiePermission
{
    use HasRoles, Uuid;

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
        'roles',
    ];
}
