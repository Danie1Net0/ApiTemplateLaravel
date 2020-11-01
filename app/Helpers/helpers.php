<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Gera token único.
 *
 * @param BaseRepository $repository
 * @param string $tokenName
 * @param int $length
 * @return string
 */
function tokenGenerate(BaseRepository $repository, string $tokenName, int $length = 6): string
{
    do {
        $token = rand(pow(10, $length - 1), pow(10, $length) - 1);
        $tokenExists = !is_null($repository->findWhere([[$tokenName, '=', $token]])->first());
    } while ($tokenExists);

    return $token;
}

/**
 * Realiza filtragem de recursos.
 *
 * @param BaseRepository $repository
 * @param Request $request
 * @return Builder
 * @throws RepositoryException
 */
function filterResources(BaseRepository  $repository, Request $request): Builder
{
    $model = $repository->makeModel();
    $query = $model->newQuery();

    if ($request->has('conditions')) {
        foreach (explode(';', $request->get('conditions')) as $conditions) {
            $params = explode(':', $conditions);
            $query->where($params[0], $params[1], $params[2]);
        }
    }

    if ($request->has('or-conditions')) {
        foreach (explode(';', $request->get('or-conditions')) as $orConditions) {
            $params = explode(':', $orConditions);
            $query->orWhere($params[0], $params[1], $params[2]);
        }
    }

    $query->role($request->has('roles') ? explode(',', $request->get('roles')) : 'Usuário');

    if ($request->has('relationships')) {
        $query->with(explode(',', $request->get('relationships')));
    }

    return $query;
}

/**
 * Obtém lista ou paginação dos recursos por meio da instância do repositório.
 *
 * @param BaseRepository $repository
 * @param Request $request
 * @return LengthAwarePaginator|Collection|mixed
 */
function getResources(BaseRepository $repository, Request $request)
{
    $columns = $request->has('columns') ? explode(',', $request->get('columns')) : ['*'];

    return $request->get('paginate') ?
        $repository->paginate($request->get('paginate'), $columns) :
        $repository->get($columns);
}