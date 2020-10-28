<?php

namespace App\Traits\Auth;

use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\Shared\MessageResponseResource;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Repositories\Auth\PasswordResetRepositoryEloquent;
use App\Repositories\Users\UserRepositoryEloquent;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Trait ResetPasswords
 * @package App\Traits\Auth
 */
trait ResetPasswords
{
    /**
     * @var UserRepositoryEloquent
     */
    private UserRepositoryEloquent $userRepository;

    /**
     * @var PasswordResetRepositoryEloquent
     */
    private PasswordResetRepositoryEloquent $passwordResetRepository;

    /**
     * VerificationController constructor.
     *
     * @param UserRepositoryEloquent $userRepository
     * @param PasswordResetRepositoryEloquent $passwordResetRepository
     */
    public function __construct(
        UserRepositoryEloquent $userRepository,
        PasswordResetRepositoryEloquent $passwordResetRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/registration/forgot-password",
     *     summary="Solicitar recuperação de senha do usuário. O usuário deverá se estar ativo.",
     *     description="Solicita envio do código de recuperação de senha do usuário por e-mail ou SMS.",
     *     tags={"Recuperação de Senha"},
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
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do usuário para envio do código de recuperação de senha.",
     *         @OA\JsonContent(
     *             required={"email", "phone"},
     *             @OA\Property(property="email", type="string", example="fulano@dominio.com"),
     *             @OA\Property(property="phone", type="string", example="17987654321"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recuperação de senha enviada com sucesso.",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado por não existir ou não estar ativo.",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validação dos parâmetros.",
     *     )
     * )
     *
     * @param ResendVerificationRequest $request
     * @return MessageResponseResource
     * @throws ValidatorException
     */
    public function request(ResendVerificationRequest $request): MessageResponseResource
    {
        $user = $this->findUser($request);

        if (is_null($user)) {
            throw new ModelNotFoundException('Usuário não encontrado.');
        }

        $passwordReset = $user->passwordReset;
        $token = tokenGenerate($this->passwordResetRepository, 'token');

        if ($passwordReset) {
            if (Carbon::parse($passwordReset->updated_at)->addMinutes(60)->isPast()) {
                $passwordReset = $this->passwordResetRepository->update(['token' => $token], $passwordReset->id);
            }
        } else {
            $passwordReset = $this->passwordResetRepository->create([
                'user_id' => $user->id,
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'token' => $token
            ]);
        }

        Notification::send($passwordReset, new ResetPasswordNotification());

        return new MessageResponseResource('Recuperação de senha enviada com sucesso!');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/registration/reset-password",
     *     summary="Recuperar senha do usuário",
     *     description="Realiza recuperação de senha do usuário por meio de código de recuperação enviado por e-mail ou SMS.",
     *     tags={"Recuperação de Senha"},
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
     *         description="Senha",
     *         in="query",
     *         name="password",
     *         required=true,
     *         example="password"
     *     ),
     *     @OA\Parameter(
     *         description="Confirmação de senha",
     *         in="query",
     *         name="password_confirmation",
     *         required=true,
     *         example="password"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do usuário para reenvio do código de confirmação",
     *         @OA\JsonContent(
     *             required={"email", "phone", "token", "password", "password_confirmation"},
     *             @OA\Property(property="email", type="string", example="fulano@dominio.com"),
     *             @OA\Property(property="phone", type="string", example="17987654321"),
     *             @OA\Property(property="token", type="string", example="952861"),
     *             @OA\Property(property="password", type="string", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", example="password"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Senha recuperada com sucesso.",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Token de recuperação de senha expirado ou inválido.",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado por não existir ou não estar ativo.",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validação dos parâmetros.",
     *     )
     * )
     *
     * @param ResetPasswordRequest $request
     * @return mixed
     */
    public function reset(ResetPasswordRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $user = $this->findUser($request);

            if (is_null($user)) {
                throw new ModelNotFoundException('Usuário não encontrado.');
            }

            if (Carbon::parse($user->passwordReset->updated_at)->addMinutes(60)->isPast()) {
                throw new AuthorizationException('Token de recuperação de senha expirado ou inválido.');
            }

            $this->userRepository->update($request->only('password'), $user->id);
            $this->passwordResetRepository->delete($user->passwordReset->id);

            return new MessageResponseResource('Senha recuperada com sucesso!');
        });
    }

    /**
     * @param FormRequest $request
     * @return User|null
     */
    private function findUser(FormRequest $request): ?User
    {
        return $this->userRepository->scopeQuery(function ($query) use ($request) {
            return $query->where(function ($query) use ($request) {
                return $query->where('email', $request->get('email'))
                    ->orWhere('cell_phone', $request->get('phone'));
            })
            ->where('is_active', true);
        })->first();
    }
}
