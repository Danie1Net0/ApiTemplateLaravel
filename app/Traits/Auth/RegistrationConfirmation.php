<?php

namespace App\Traits\Auth;

use App\Http\Requests\Auth\ConfirmationRegistrationRequest;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Resources\Shared\MessageResponseResource;
use App\Http\Resources\Users\UserResource;
use App\Notifications\Auth\RegistrationConfirmationNotification;
use App\Repositories\Implementations\Users\UserRepositoryEloquent;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

/**
 * Trait RegistrationConfirmation
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
     * @param UserRepositoryEloquent $userRepository
     */
    public function __construct(UserRepositoryEloquent $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param ConfirmationRegistrationRequest $request
     * @return UserResource
     */
    public function confirmation(ConfirmationRegistrationRequest $request): UserResource
    {
        $user = $this->findUser($request);

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
     * @param ResendVerificationRequest $request
     * @return MessageResponseResource
     */
    public function resendCode(ResendVerificationRequest $request): MessageResponseResource
    {
        $user = $this->findUser($request);

        if (is_null($user)) {
            throw new ModelNotFoundException('Código de confirmação não encontrado.');
        }

        Notification::send($user, new RegistrationConfirmationNotification());

        return new MessageResponseResource('Código de confirmação reenviado com sucesso!');
    }

    /**
     * @param FormRequest $request
     * @return User|null
     */
    public function findUser(FormRequest $request): ?User
    {
        return $this->userRepository->scopeQuery(function ($query) use ($request) {
            return $query->where(function ($query) use ($request) {
                if ($request->has('email')) {
                    $query->where('email', $request->get('email'));
                }

                if ($request->has('phone')) {
                    $query->orWhere('cell_phone', $request->get('phone'));
                }

                return $query;
            })->where('confirmation_token', '<>', null);
        })->first();
    }
}
