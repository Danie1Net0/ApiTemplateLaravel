<?php

namespace App\Repositories\Implementations\Auth;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\Auth\PasswordResetRepository;
use App\Models\Auth\PasswordReset;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class PasswordResetRepositoryEloquent
 * @package App\Repositories\Implementations\Auth
 */
class PasswordResetRepositoryEloquent extends BaseRepository implements PasswordResetRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PasswordReset::class;
    }


    /**
     * Boot up the repository, pushing criteria
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
