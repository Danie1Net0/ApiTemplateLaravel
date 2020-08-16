<?php

namespace App\Traits\Auth;

use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Requests\Auth\EmailVerificationRequest;
use App\Http\Requests\Auth\TokenValidationRequest;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Shared\MessageResponseResource;
use App\Notifications\Auth\EmailVerificationNotification;
use App\Repositories\Users\UserRepositoryEloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Trait VerifiesEmails
 *
 * @package App\Traits\Auth
 */
trait VerifiesEmails
{
    /**
     * @var UserRepositoryEloquent
     */
    private $userRepository;

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
     * @param EmailVerificationRequest $request
     * @return UserResource
     * @throws RepositoryException
     */
    public function verifyEmail(EmailVerificationRequest $request): UserResource
    {
        $user = $this->userRepository->findWhere([
            'id' => $request->id,
            'activation_token' => $request->activation_token
        ])->first();

        $attributes = [
            'is_active' => true,
            'activation_token' => null
        ];

        if ($request->password)
            $attributes = array_merge($attributes, ['password' => Hash::make($request->password)]);

        if (is_null($user))
            throw new ModelNotFoundException('Usuário não encontrado.');

        $this->userRepository->update($attributes, $request->id);

        $token = $user->createToken('Personal Access Token');

        return (new UserResource($user))->additional([
            'data' => [
                'message' => 'Cadastro finalizado com sucesso!'
            ],
            'meta' => [
                'token' => $token->accessToken,
                'expires_at' => Carbon::parse($token->token->expires_at)->toDateTimeString()
            ]]);
    }

    /**
     * @param TokenValidationRequest $request
     * @return MessageResponseResource
     */
    public function verifyToken(TokenValidationRequest $request): MessageResponseResource
    {
        $user = $this->userRepository->findWhere([
            'id' => $request->id,
            'activation_token' => $request->activation_token
        ])->first();

        if (is_null($user))
            throw new ModelNotFoundException('Token de verificação não encontrado.');

        $message = [
            'success' => true,
            'message' => 'Token de verificação validado com sucesso!'
        ];

        if (is_null($user->password))
            $message = array_merge($message, ['password_required' => true]);

        return new MessageResponseResource($message);
    }

    /**
     * @param ResendVerificationRequest $request
     * @return MessageResponseResource
     */
    public function resend(ResendVerificationRequest $request): MessageResponseResource
    {
        $user = $this->userRepository->findWhere([
            'email' => $request->email,
            ['activation_token', '<>', null]
        ])->first();

        if (is_null($user))
            throw new ModelNotFoundException('O usuário já está ativo.');

        Notification::send($user, new EmailVerificationNotification());

        return (new MessageResponseResource(['success' => true, 'message' => 'Verificação reenviada com sucesso!']));
    }
}
