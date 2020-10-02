<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\IndexUserRequest;
use App\Http\Requests\Users\ShowUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\Shared\MessageResponseResource;
use App\Http\Resources\Users\UserResource;
use App\Repositories\Users\UserRepositoryEloquent;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class UserController
 * @package App\Http\Controllers\Users
 */
class UserController extends Controller
{
    /**
     * @var UserRepositoryEloquent
     */
    private UserRepositoryEloquent $userRepository;

    /**
     * UserController constructor.
     * @param UserRepositoryEloquent $userRepository
     */
    public function __construct(UserRepositoryEloquent $userRepository)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:Listar Usuário')->only('index');
        $this->middleware('permission:Visualizar Usuário')->only('show');
        $this->middleware('permission:Editar Usuário')->only('update');
        $this->middleware('permission:Deletar Usuário')->only('destroy');

        $this->userRepository = $userRepository;
    }

    /**
     * @param IndexUserRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexUserRequest $request)
    {
        $users = $this->userRepository
            ->with($request->relationships ?? ['avatar', 'telephones', 'roles', 'permissions'])
            ->scopeQuery(function ($query) use ($request) {
                return $query->where($request->search ?? [])->role($request->roles ?? 'Usuário');
            });

        $users = $request->paginate ?
            $users->paginate($request->paginate, $request->columns ?? ['*']) :
            $users->get($request->columns ?? ['*']);

        return UserResource::collection($users);
    }

    /**
     * @param CreateUserRequest $request
     * @return UserResource
     * @throws RepositoryException
     */
    public function store(CreateUserRequest $request): UserResource
    {
        $user = $this->userRepository->create($request->all());
        return (new UserResource($user))->additional(['data' => ['message' => 'Usuário cadastrado com sucesso!']]);
    }

    /**
     * @param ShowUserRequest $request
     * @param int $id
     * @return UserResource
     */
    public function show(ShowUserRequest $request, int $id): UserResource
    {
        $user = $this->userRepository
            ->with($request->relationships ?? ['avatar', 'telephones', 'roles', 'permissions'])
            ->find($id, $request->columns ?? ['*']);

        return new UserResource($user);
    }

    /**
     * @param UpdateUserRequest $request
     * @param int $id
     * @return UserResource
     * @throws RepositoryException
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $user = $this
            ->userRepository
            ->with($request->relationships ?? ['avatar', 'telephones', 'roles'])
            ->update($request->all(), $id);

        return (new UserResource($user))->additional(['data' => ['message' => 'Usuário atualizado com sucesso!']]);
    }

    /**
     * @param int $id
     * @return MessageResponseResource
     * @throws RepositoryException
     */
    public function destroy(int $id): MessageResponseResource
    {
        $this->userRepository->delete($id);
        return new MessageResponseResource(['success' => true, 'message' => 'Usuário removido com sucesso!']);
    }
}
