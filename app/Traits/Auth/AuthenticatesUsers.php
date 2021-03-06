<?php

namespace App\Traits\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Shared\MessageResponseResource;
use App\Repositories\Implementations\Users\UserRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait AuthenticatesUsers
 * @package App\Traits\Auth
 */
trait AuthenticatesUsers
{
    /**
     * @param LoginRequest $request
     * @param UserRepositoryEloquent $userRepository
     * @return JsonResponse|object
     */
    public function login(LoginRequest $request, UserRepositoryEloquent $userRepository)
    {
        if ($request->has('email')) {
            $user = $userRepository->findWhere(['email' => $request->get('email')])->first();
        } else {
            $user = $userRepository->findWhere(['cell_phone' => $request->get('phone')])->first();
        }

        $authenticated = false;

        if (isset($user) && Hash::check($request->get('password'), $user->password)) {
            $authenticated = true;
        }

        if (!$authenticated || (!$user->is_active && is_null($user->confirmation_token))) {
            return (new MessageResponseResource('E-mail ou senha inválidos.'))
                ->response()
                ->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        if ($user->is_active) {
            $user = $userRepository
                ->with(['avatar', 'telephones', 'roles', 'permissions'])
                ->find($user->id);

            $token = $user->createToken('Personal Access Token');

            return (new UserResource($user))->additional([
                'data' => ['token' => $token->plainTextToken],
                'meta' => ['message' => 'Usuário autenticado com sucesso!']
            ]);
        }

        return (new MessageResponseResource([
            'description' => 'Cadastro não verificado.',
            'resend_verification' => [
                'url' => url(route('resend_verification')),
                'method' => 'POST'
            ]]))
            ->response()
            ->setStatusCode(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return MessageResponseResource
     */
    public function logout(): MessageResponseResource
    {
        Auth::user()->currentAccessToken()->delete();
        return new MessageResponseResource('Desconectado com sucesso!');
    }
}
