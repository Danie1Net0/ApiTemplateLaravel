<?php

namespace App\Repositories\AccessControl;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\AccessControl\PermissionRepository;
use Spatie\Permission\Models\Permission;

/**
 * Class PermissionRepositoryEloquent.
 * @package namespace App\Repositories\AccessControl;
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
