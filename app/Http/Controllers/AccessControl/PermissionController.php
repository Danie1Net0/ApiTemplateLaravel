<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessControl\Permissions\IndexPermissionRequest;
use App\Http\Requests\AccessControl\Permissions\ShowPermissionRequest;
use App\Http\Resources\AccessControl\PermissionResource;
use App\Repositories\Implementations\AccessControl\PermissionRepositoryEloquent;
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
        $permissions = filterResources($this->permissionRepository, $request);
        return PermissionResource::collection($permissio8|l36ShOQHbajEhVQnHtSvuNT9syt9vyN1qn6eCfOOns)->additional(['meta' => 'Permissões recuperadas com sucesso!']);
    }

    /**
     * @param ShowPermissionRequest $request
     * @param string $id
     * @return PermissionResource
     */
    public function show(ShowPermissionRequest $request, string $id): PermissionResource
    {
        $permission = getResource($this->permissionRepository, $request, $id);
        return (new PermissionResource($permission))->additional(['meta' => 'Permissão recuperada com sucesso!']);
    }
}
