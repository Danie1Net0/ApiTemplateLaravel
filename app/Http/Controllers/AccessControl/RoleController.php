<?php

namespace App\Http\Controllers\AccessControl;

use App\Exceptions\DeleteResourceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccessControl\Roles\CreateRoleRequest;
use App\Http\Requests\AccessControl\Roles\IndexRoleRequest;
use App\Http\Requests\AccessControl\Roles\ShowRoleRequest;
use App\Http\Requests\AccessControl\Roles\UpdateRoleRequest;
use App\Http\Resources\AccessControl\RoleResource;
use App\Http\Resources\Shared\MessageResponseResource;
use App\Repositories\Implementations\AccessControl\RoleRepositoryEloquent;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class RoleController
 * @package App\Http\Controllers\AccessControl
 */
class RoleController extends Controller
{
    /**
     * @var RoleRepositoryEloquent
     */
    private RoleRepositoryEloquent $roleRepository;

    /**
     * RoleController constructor.
     * @param RoleRepositoryEloquent $roleRepository
     */
    public function __construct(RoleRepositoryEloquent $roleRepository)
    {
        $this->middleware(['auth:api', 'verify_permission']);

        $this->roleRepository = $roleRepository;
    }

    /**
     * @param IndexRoleRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRoleRequest $request): AnonymousResourceCollection
    {
        $roles = filterResources($this->roleRepository, $request);
        return RoleResource::collection($roles)->additional(['meta' => ['message' => 'Funções recuperadas com sucesso!']]);
    }

    /**
     * @param CreateRoleRequest $request
     * @return RoleResource
     * @throws RepositoryException
     */
    public function store(CreateRoleRequest $request): RoleResource
    {
        $role = $this->roleRepository->create($request->all());
        return (new RoleResource($role))->additional(['meta' => ['message' => 'Função cadastrada com sucesso!']]);
    }

    /**
     * @param ShowRoleRequest $request
     * @param string $id
     * @return RoleResource
     */
    public function show(ShowRoleRequest $request, string $id): RoleResource
    {
        $role = getResource($this->roleRepository, $request, $id);
        return (new RoleResource($role))->additional(['meta' => ['message' => 'Função recuperada com sucesso!']]);
    }

    /**
     * @param UpdateRoleRequest $request
     * @param string $id
     * @return RoleResource
     * @throws RepositoryException
     */
    public function update(UpdateRoleRequest $request, string $id)
    {
        $role = $this->roleRepository->update($request->all(), $id);
        return (new RoleResource($role))->additional(['meta' => ['message' => 'Função atualizada com sucesso!']]);
    }

    /**
     * @param string $id
     * @return MessageResponseResource
     * @throws DeleteResourceException
     */
    public function destroy(string $id): MessageResponseResource
    {
        try {
            $this->roleRepository->delete($id);
            return new MessageResponseResource('Função removida com successo!');
        } catch (Exception $exception) {
            throw new DeleteResourceException($exception->getMessage());
        }
    }
}
