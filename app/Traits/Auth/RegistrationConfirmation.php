<?php

namespace App\Traits\Auth;

use App\Http\Requests\Auth\ConfirmationRegistrationRequest;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Resources\Shared\MessageResponseResource;
use App\Http\Resources\Users\UserResource;
use App\Notifications\Auth\RegistrationConfirmationNotification;
use App\Repositories\Users\UserRepositoryEloquent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

/**
 * Trait RegistrationConfirmation
 *
 * @package App\Traits\Auth
 */
trait RegistrationConfirmation
{
    /**
     * @var UserRepositoryEloquent
     */
    private UserRepositoryEloquent $userRepository;

    /**
     * VerificationController constructor.
     *
     * @param UserRepositoryEloquent $userRepository
     */
    public function __construct(UserRepositoryEloquent $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/registration/confirmation",
     *     summary="Confirmar cadastro do usuário",
     *     description="Confirmação de cadastro do usuário por meio do código enviado por e-mail ou SMS. Se o usuário não tiver uma senha cadastrada, será necessário enviar a senha e sua confirmação.",
     *     tags={"Confirmação de Cadastro"},
     *     @OA\Parameter(
     *         description="E-mail do usuário (Obrigatório apenas se não for enviado o número de celular)",
     *         in="query",
     *         name="email",
     *         required=true,
     *         example="fulano@dominio.com"
     *     ),
     *     @OA\Parameter(
     *         description="Número de celular do usuário (Obrigatório apenas se não for enviado o e-mail)",
     *         in="query",
     *         name="phone",
     *         required=true,
     *         example="fulano@dominio.com"
     *     ),
     *     @OA\Parameter(
     *         description="Código de confirmação (Enviado para o usuário por e-mail ou SMS)",
     *         in="query",
     *         name="token",
     *         required=true,
     *         example=952861
     *     ),
     *     @OA\Parameter(
     *         description="Senha (Quando for obrigatória, será retornada uma mensagem de validação informando)",
     *         in="query",
     *         name="password",
     *         required=false,
     *         example="password"
     *     ),
     *     @OA\Parameter(
     *         description="Confirmação de senha (Quando for obrigatória, será retornada uma mensagem de validação informando)",
     *         in="query",
     *         name="password_confirmation",
     *         required=false,
     *         example="password"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados para confirmação do cadastro.",
     *         @OA\JsonContent(
     *             required={"email", "phone", "token"},
     *             @OA\Property(property="email", type="string", example="fulano@dominio.com"),
     *             @OA\Property(property="phone", type="string", example="17987654321"),
     *             @OA\Property(property="token", type="string", example="952861"),
     *             @OA\Property(property="password", type="string", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", example="password"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cadastro confirmado com sucesso. Retorna os dados do usuário com o token de autenticação.",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Código de verificação inválido.",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validação dos parâmetros.",
     *     )
     * )
     *
     * @param ConfirmationRegistrationRequest $request
     * @return UserResource
     */
    public function confirmation(ConfirmationRegistrationRequest $request): UserResource
    {
        $user = $this->userRepository->scopeQuery(function ($query) use ($request) {
            return $query->where(function ($query) use ($request) {
                return $query->where('email', $request->get('email'))
                    ->orWhereHas('telephones', function ($query) use ($request) {
                        return $query->where('number', $request->get('phone'));
                    });
            })
            ->where('confirmation_token', $request->get('token'));
        })->first();

        $attributes = ['is_active' => true, 'confirmation_token' => null];

        if ($request->has('password')) {
            $attributes = array_merge($attributes, ['password' => Hash::make($request->get('password'))]);
        }

        if (is_null($user)) {
            throw new ModelNotFoundException('Código de verificação inválido.');
        }

        $user = $this->userRepository->update($attributes, $user->id);

        return (new UserResource($user))->additional([
            'data' => ['token' => $user->createToken('Personal Access Token')->plainTextToken],
            'meta' => ['message' => 'Cadastro confirmado com sucesso!']
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/registration/confirmation/resend-code",
     *     summary="Reenviar código de confirmação de cadastro",
     *     description="Reenvia código de confirmação de cadastro por e-mail ou SMS.",
     *     tags={"Confirmação de Cadastro"},
     *     @OA\Parameter(
     *         description="E-mail do usuário (Obrigatório apenas se não for enviado o número de celular)",
     *         in="query",
     *         name="email",
     *         required=true,
     *         example="fulano@dominio.com",
     *     ),
     *     @OA\Parameter(
     *         description="Número de celular do usuário (Obrigatório apenas se não for enviado o e-mail)",
     *         in="query",
     *         name="phone",
     *         required=true,
     *         example="fulano@dominio.com"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do usuário para reenvio do código de confirmação.",
     *         @OA\JsonContent(
     *             required={"email", "phone"},
     *             @OA\Property(property="email", type="string", example="fulano@dominio.com"),
     *             @OA\Property(property="phone", type="string", example="17987654321"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Código reenviado com sucesso.",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Código não encontrado devido ao usuário já estar ativo ou não existir.",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validação dos parâmetros.",
     *     )
     * )
     *
     * @param ResendVerificationRequest $request
     * @return MessageResponseResource
     */
    public function resendCode(ResendVerificationRequest $request): MessageResponseResource
    {
        $user = $this->userRepository->scopeQuery(function ($query) use ($request) {
            return $query->where(function ($query) use ($request) {
                return $query->where('email', $request->get('email'))
                    ->orWhereHas('telephones', function ($query) use ($request) {
                        return $query->where('number', $request->get('phone'));
                    });
            })
            ->where('confirmation_token', '<>', null);
        })->first();

        if (is_null($user)) {
            throw new ModelNotFoundException('Código de confirmação não encontrado.');
        }

        Notification::send($user, new RegistrationConfirmationNotification());

        return new MessageResponseResource('Código de confirmação reenviado com sucesso!');
    }
}
