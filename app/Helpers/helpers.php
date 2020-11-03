<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Prettus\Repository\Eloquent\BaseRepository;

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
 * Obtém recursos através do repositório.
 *
 * @param BaseRepository $repository
 * @param Request $request
 * @param bool $searchByUserRole
 * @return BaseRepository
 */
function getResources(BaseRepository $repository, Request $request, bool $searchByUserRole = false): BaseRepository
{
    return $repository->scopeQuery(function ($query) use ($repository, $request, $searchByUserRole) {
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

        if ($searchByUserRole) {
            $query->role($request->has('roles') ? explode(',', $request->get('roles')) : 'Usuário');
        }

        if ($request->has('relationships')) {
            $query->with(explode(',', $request->get('relationships')));
        }

        if ($request->has('order')) {
            $order = explode(':', $request->get('order'));
            $query->orderBy($order[0], $order[1] ?? 'asc');
        }

        return $query;
    });
}

/**
 * Obtém lista ou paginação dos recursos por meio da instância do repositório.
 *
 * @param BaseRepository $repository
 * @param Request $request
 * @param bool $searchByUserRole
 * @return LengthAwarePaginator|Collection|mixed
 */
function filterResources(BaseRepository $repository, Request $request, bool $searchByUserRole = false)
{
    $resources = getResources($repository, $request, $searchByUserRole);

    $columns = $request->has('columns') ? explode(',', $request->get('columns')) : ['*'];

    if (!in_array('id', $columns)) {
        array_unshift($columns, 'id');
    }

    return $request->get('paginate') ?
        $resources->paginate($request->get('paginate'), $columns) :
        $resources->get($columns);
}

/**
 * Obtém recurso único através do repositório.
 *
 * @param BaseRepository $repository
 * @param Request $request
 * @param string $id
 * @return LengthAwarePaginator|Collection|mixed
 */
function getResource(BaseRepository $repository, Request $request, string $id)
{
    $relationships = $request->has('relationships') ? explode(',', $request->get('relationships')) : [];
    $columns = $request->has('columns') ? explode(',', $request->get('columns')) : ['*'];

    if (!in_array('id', $columns)) {
        array_unshift($columns, 'id');
    }

    return $repository->with($relationships)->find($id, $columns);
}

