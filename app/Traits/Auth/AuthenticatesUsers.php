<?php

namespace App\Traits\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Shared\MessageResponseResource;
use App\Repositories\Users\UserRepositoryEloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
        $credentials = $request->only('email', 'password');
        $attempt = Auth::attempt($credentials);

        if (!$attempt || ($attempt && !Auth::user()->is_active && is_null(Auth::user()->activation_token))) {
            return (new MessageResponseResource(['success' => false, 'message' => 'E-mail ou senha inválidos.']))
                ->response()
                ->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        if (Auth::user()->is_active) {
            $user = $userRepository
                ->with(['avatar', 'telephones', 'roles'])
                ->find(Auth::id());

            $tokenName = 'Personal Token ' . Auth::id();
            $token = $user->createToken($tokenName);

            return (new UserResource($user))->additional(['meta' => ['token' => $token->plainTextToken]]);
        }

        return (new MessageResponseResource([
            'success' => false,
            'message' => 'Cadastro não verificado.',
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

        return (new MessageResponseResource(['success' => true, 'message' => 'Desconectado com sucesso!']));
    }
}
