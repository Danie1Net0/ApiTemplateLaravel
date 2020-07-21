<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessControl\Permissions\IndexPermissionRequest;
use App\Http\Resources\AccessControl\PermissionResource;
use App\Repositories\AccessControl\PermissionRepositoryEloquent;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class PermissionController
 * @package App\Http\Controllers\AccessControl
 */
class PermissionController extends Controller
{
    /**
     * @var PermissionRepositoryEloquent
     */
    private $permissionRepository;

    /**
     * PermissionController constructor.
     * @param PermissionRepositoryEloquent $permissionRepository
     */
    public function __construct(PermissionRepositoryEloquent $permissionRepository)
    {
        $this->middleware(['auth:api', 'permission:listar-permissao'])->only('index');
        $this->middleware(['auth:api', 'permission:visualizar-permissao'])->only('show');

        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param IndexPermissionRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexPermissionRequest $request): AnonymousResourceCollection
    {
        $permissions = $this->permissionRepository->scopeQuery(function ($query) use ($request) {
            return $query->where($request->search ?? []);
        })->paginate($request->paginate ?? 10);

        return PermissionResource::collection($permissions);
    }

    /**
     * @param int $id
     * @return PermissionResource
     */
    public function show(int $id): PermissionResource
    {
        $permission = $this->permissionRepository->find($id);
        return new PermissionResource($permission);
    }
}
