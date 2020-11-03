<?php

namespace App\Repositories\Implementations\AccessControl;

use App\Models\AccessControl\Permission;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\AccessControl\PermissionRepository;

/**
 * Class PermissionRepositoryEloquent
 * @package App\Repositories\Implementations\AccessControl
 */
class PermissionRepositoryEloquent extends BaseRepository implements PermissionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Permission::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
