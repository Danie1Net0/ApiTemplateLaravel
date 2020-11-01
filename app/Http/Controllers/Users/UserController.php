<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\IndexUserRequest;
use App\Http\Requests\Users\ShowUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\Shared\MessageResponseResource;
use App\Http\Resources\Users\UserResource;
use App\Repositories\Implementations\Users\UserRepositoryEloquent;
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
        $this->middleware(['auth:sanctum', 'verify_permission'])->except('store');

        $this->userRepository = $userRepository;
    }

    /**
     * @param IndexUserRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexUserRequest $request)
    {
        $users = $this->userRepository->scopeQuery(function ($query) use ($request) {
            $query = filterResources($this->userRepository, $request);
            $query->role($request->has('roles') ? explode(',', $request->get('roles')) : 'Usuário');
        });
        $users = getResources($users, $request);

        return UserResource::collection($users)->additional(['meta' => 'Usuários recuperados com sucesso!']);
    }

    /**
     * @param CreateUserRequest $request
     * @return UserResource
     */
    public function store(CreateUserRequest $request): UserResource
    {
        $user = $this->userRepository->create($request->all());
        return (new UserResource($user))->additional(['meta' => ['message' => 'Usuário cadastrado com sucesso!']]);
    }

    /**
     * @param ShowUserRequest $request
     * @param int $id
     * @return UserResource
     */
    public function show(ShowUserRequest $request, int $id): UserResource
    {
        $user = $this->userRepository
            ->with($request->has('relationships') ? explode(',', $request->get('relationships')) : [])
            ->find($id, explode(',', $request->get('columns')) ?? ['*']);

        return (new UserResource($user))->additional(['meta' => 'Usuário recuperado com sucesso!']);
    }

    /**
     * @param UpdateUserRequest $request
     * @param int $id
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $user = $this->userRepository->update($request->all(), $id);
        return (new UserResource($user))->additional(['meta' => ['message' => 'Usuário atualizado com sucesso!']]);
    }

    /**
     * @param int $id
     * @return MessageResponseResource
     * @throws RepositoryException
     */
    public function destroy(int $id): MessageResponseResource
    {
        $this->userRepository->delete($id);
        return new MessageResponseResource('Usuário removido com sucesso!');
    }
}
