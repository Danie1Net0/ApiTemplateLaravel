<?php

use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Gera token único.
 *
 * @param BaseRepository $repository
 * @param string $tokenName
 * @param int $length
 * @return string
 */
function tokenGenerate(BaseRepository $repository, string $tokenName, int $length = 6): string
{
    do {
        $token = rand(pow(10, $length - 1), pow(10, $length) - 1);
        $tokenExists = !is_null($repository->findWhere([[$tokenName, '=', $token]])->first());
    } while ($tokenExists);

    return $token;
}
