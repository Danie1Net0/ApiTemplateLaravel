<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\User\CreateUserRequest;
use App\Http\Requests\Users\User\UpdateUserRequest;
use App\Http\Resources\Users\UserResource;
use App\Repositories\Users\UserRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Symfony\Component\HttpFoundation\Response;

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
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->findWhere($request->all());
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
     * @param int $id
     * @return UserResource
     */
    public function show(int $id): UserResource
    {
        $user = $this->userRepository->find($id);
        return new UserResource($user);
    }

    /**
     * @param UpdateUserRequest $request
     * @param int $id
     * @return UserResource
     * @throws ValidatorException
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $user = $this->userRepository->update($request->all(), $id);
        return new UserResource($user);
    }

    /**
     * @param int $id
     * @return HttpResponse
     */
    public function destroy(int $id): HttpResponse
    {
        $this->userRepository->delete($id);
        return response(Response::HTTP_OK);
    }
}
