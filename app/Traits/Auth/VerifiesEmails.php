<?php

namespace App\Traits\Auth;

use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Requests\Auth\VerificationRequest;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Validations\MessageResponseResource;
use App\Notifications\Auth\EmailVerificationNotification;
use App\Repositories\Users\UserRepositoryEloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Notification;
use Prettus\Validator\Exceptions\ValidatorException;

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
     * @param VerificationRequest $request
     * @return UserResource
     * @throws ValidatorException
     */
    public function verifyEmail(VerificationRequest $request): UserResource
    {
        $user = $this->userRepository->findWhere([
            'id' => $request->id,
            'activation_token' => $request->activation_token
        ])->first();

        if (is_null($user))
            throw new ModelNotFoundException('Usuário não encontrado.');

        $this->userRepository->update([
            'is_active' => true,
            'activation_token' => null
        ], $request->id);

        $token = $user->createToken('Personal Access Token');

        return (new UserResource($user))->additional(['meta' => [
            'token' => $token->accessToken,
            'expires_at' => Carbon::parse($token->token->expires_at)->toDateTimeString()
        ]]);
    }

    /**
     * @param VerificationRequest $request
     * @return MessageResponseResource
     */
    public function verifyToken(VerificationRequest $request): MessageResponseResource
    {
        $user = $this->userRepository->findWhere([
            'id' => $request->id,
            'activation_token' => $request->activation_token
        ])->first();

        if (is_null($user))
            throw new ModelNotFoundException('Token de verificação não encontrado.');

        return (new MessageResponseResource(['success' => true, 'message' => 'Token de verificação validado com sucesso!']));
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
