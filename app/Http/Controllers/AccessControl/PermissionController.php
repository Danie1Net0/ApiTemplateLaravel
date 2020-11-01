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
    private PermissionRepositoryEloquent $permissionRepository;

    /**
     * PermissionController constructor.
     * @param PermissionRepositoryEloquent $permissionRepository
     */
    public function __construct(PermissionRepositoryEloquent $permissionRepository)
    {
        $this->middleware(['auth:sanctum', 'verify_permission']);

        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param IndexPermissionRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexPermissionRequest $request): AnonymousResourceCollection
    {
        $permissions = $this->permissionRepository
            ->scopeQuery(fn () => filterResources($this->permissionRepository, $request));
        $permissions = getResources($permissions, $request);

        return PermissionResource::collection($permissions)
            ->additional(['meta' => 'Permissões recuperadas com sucesso!']);
    }

    /**
     * @param int $id
     * @return PermissionResource
     */
    public function show(int $id): PermissionResource
    {
        $permission = $this->permissionRepository->find($id);
        return (new PermissionResource($permission))->additional(['meta' => 'Permissãp recuperada com sucesso!']);
    }
}
