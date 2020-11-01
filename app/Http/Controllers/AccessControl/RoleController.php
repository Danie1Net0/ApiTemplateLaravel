<?php

namespace App\Http\Controllers\AccessControl;

use App\Exceptions\DeleteResourceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccessControl\Roles\CreateRoleRequest;
use App\Http\Requests\AccessControl\Roles\IndexRoleRequest;
use App\Http\Requests\AccessControl\Roles\UpdateRoleRequest;
use App\Http\Resources\AccessControl\RoleResource;
use App\Http\Resources\Shared\MessageResponseResource;
use App\Repositories\AccessControl\RoleRepositoryEloquent;
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
        $this->middleware('auth:api');
        $this->middleware('permission:Listar Função')->only('index');
        $this->middleware('permission:Criar Função')->only('store');
        $this->middleware('permission:Visualizar Função')->only('show');
        $this->middleware('permission:Editar Função')->only('update');
        $this->middleware('permission:Deletar Função')->only('destroy');

        $this->roleRepository = $roleRepository;
    }

    /**
     * @param IndexRoleRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRoleRequest $request): AnonymousResourceCollection
    {
        $roles = $this->roleRepository->scopeQuery(function ($query) use ($request) {
            return $query->where($request->search ?? []);
        })->paginate($request->paginate ?? 10);

        return RoleResource::collection($roles);
    }

    /**
     * @param CreateRoleRequest $request
     * @return RoleResource
     * @throws RepositoryException
     */
    public function store(CreateRoleRequest $request): RoleResource
    {
        $role = $this->roleRepository->create($request->all());
        return (new RoleResource($role))->additional(['data' => ['message' => 'Grupo de Permissões cadastrado com sucesso!']]);
    }

    /**
     * @param $id
     * @return RoleResource
     */
    public function show(int $id): RoleResource
    {
        $role = $this->roleRepository->find($id);
        return new RoleResource($role);
    }

    /**
     * @param UpdateRoleRequest $request
     * @param int $id
     * @return RoleResource
     * @throws RepositoryException
     */
    public function update(UpdateRoleRequest $request, int $id)
    {
        $role = $this->roleRepository->update($request->all(), $id);
        return (new RoleResource($role))->additional(['data' => ['message' => 'Grupo de Permissões atualizado com sucesso!']]);
    }

    /**
     * @param int $id
     * @return MessageResponseResource
     * @throws Exception
     */
    public function destroy(int $id): MessageResponseResource
    {
        try {
            $this->roleRepository->delete($id);
            return new MessageResponseResource(['success' => true, 'message' => 'Grupo de Permissões removido com successo!']);
        } catch (Exception $exception) {
            throw new DeleteResourceException($exception->getMessage());
        }
    }
}
