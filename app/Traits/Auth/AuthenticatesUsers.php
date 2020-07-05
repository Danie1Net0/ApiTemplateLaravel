<?php

namespace App\Traits\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Validations\MessageResponseResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait AuthenticatesUsers
 *
 * @package App\Traits\Auth
 */
trait AuthenticatesUsers
{
    /**
     * @param LoginRequest $request
     * @return JsonResponse|object
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::guard('web')->attempt($credentials))
            return (new MessageResponseResource(['success' => false, 'message' => 'E-mail ou senha inválidos.']))
                ->response()
                ->setStatusCode(Response::HTTP_UNAUTHORIZED);

        if (Auth::guard('web')->user()->is_active) {
            $user = Auth::guard('web')->user();
            $token = $user->createToken('Personal Access Token');

            return (new UserResource($user))->additional(['meta' => [
                'token' => $token->accessToken,
                'expires_at' => Carbon::parse($token->token->expires_at)->toDateTimeString()
            ]]);
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
     * @param Request $request
     * @return MessageResponseResource
     */
    public function logout(Request $request): MessageResponseResource
    {
        $request->user()->token()->revoke();

        return (new MessageResponseResource(['success' => true, 'message' => 'Desconectado com sucesso!']));
    }
}
