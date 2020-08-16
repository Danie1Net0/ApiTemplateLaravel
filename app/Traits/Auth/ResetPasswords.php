<?php

namespace App\Traits\Auth;

use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\Shared\MessageResponseResource;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Repositories\Users\UserRepositoryEloquent;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Trait ResetPasswords
 * @package App\Traits\Auth
 */
trait ResetPasswords
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
     * @param ResendVerificationRequest $request
     * @return MessageResponseResource
     */
    public function request(ResendVerificationRequest $request): MessageResponseResource
    {
        $user = $this->userRepository->findWhere(['email' => $request->email])->first();

        $passwordReset = $user->passwordReset;

        if ($passwordReset)
            $passwordReset->update(['token' => Str::random(60)]);
        else
            $passwordReset = $user->passwordReset()->create([
                'email' => $user->email,
                'token' => Str::random(60)
            ]);

        Notification::send($passwordReset, new ResetPasswordNotification());

        return new MessageResponseResource(['success' => true, 'message' => 'Recuperação de senha enviada com sucesso!']);
    }

    /**
     * @param ResetPasswordRequest $request
     * @return MessageResponseResource
     * @throws AuthorizationException|RepositoryException
     */
    public function reset(ResetPasswordRequest $request): MessageResponseResource
    {
        $user = $this->userRepository->find($request->user_id);

        if (Carbon::parse($user->passwordReset->updated_at)->addMinutes(60)->isPast())
            throw new AuthorizationException('Token de recuperação de senha expirado ou inválido.');

        $this->userRepository->update($request->only('password'), $request->user_id);
        $user->passwordReset()->delete();

        return new MessageResponseResource(['success' => true, 'message' => 'Senha recuperada com sucesso!']);
    }

}
