<?php

namespace App\Criteria\Users;

use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class UpdateUserCriteria.
 *
 * @package namespace App\Criteria\Users;
 */
class UpdateUserCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if (Auth::user() && !Auth::user()->hasRole('Super Administrador|Administrador')) {
            $model = $model->where('id', Auth::id());
        }

        return $model;
    }
}
