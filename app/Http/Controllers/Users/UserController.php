<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Users\CreateUserRequest;
use App\Http\Requests\Users\Users\IndexUserRequest;
use App\Http\Requests\Users\Users\ShowUserRequest;
use App\Http\Requests\Users\Users\UpdateUserRequest;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Validations\MessageResponseResource;
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
    private $userRepository;

    /**
     * UserController constructor.
     * @param UserRepositoryEloquent $userRepository
     */
    public function __construct(UserRepositoryEloquent $userRepository)
    {
        $this->middleware(['auth:api', 'permission:Listar Usuário'])->only('index');
        $this->middleware(['auth:api', 'permission:Visualizar Usuário'])->only('show');
        $this->middleware(['auth:api', 'permission:Editar Usuário'])->only('update');
        $this->middleware(['auth:api', 'permission:Deletar Usuário'])->only('destroy');

        $this->userRepository = $userRepository;
    }

    /**
     * @param IndexUserRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexUserRequest $request)
    {
        $users = $this->userRepository->scopeQuery(function ($query) use ($request) {
            return $query->where($request->search ?? [])->role($request->roles ?? 'Usuário');
        })->paginate($request->paginate ?? 10, $request->columns ?? ['*']);

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
        return new UserResource($user);
    }

    /**
     * @param ShowUserRequest $request
     * @param int $id
     * @return UserResource
     */
    public function show(ShowUserRequest $request, int $id): UserResource
    {
        $user = $this->userRepository->find($id, $request->columns ?? ['*']);
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
        $user = $this->userRepository->update($request->all(), $id);
        return new UserResource($user);
    }

    /**
     * @param int $id
     * @return MessageResponseResource
     */
    public function destroy(int $id): MessageResponseResource
    {
        $this->userRepository->delete($id);
        return new MessageResponseResource(['success' => true, 'message' => 'Usuário removido com sucesso!']);
    }
}
