<?php

use App\Repositories\Users\UserRepositoryEloquent;

/**
 * Gera token de confirmação único.
 *
 * @param UserRepositoryEloquent $userRepository
 * @return string
 */
function confirmationTokenGenerate(UserRepositoryEloquent $userRepository): string
{
    do {
        $confirmationToken = rand(100000, 999999);
        $tokenExists = !is_null($userRepository->findWhere([['confirmation_token', '=', $confirmationToken]])->first());
    } while ($tokenExists);

    return $confirmationToken;
}
