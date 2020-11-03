<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VerifyAcessPermission
 * @package App\Http\Middleware\Auth
 */
class VerifyAcessPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $permissionName = $request->route()->getName();

        if (!$request->user()->can($permissionName)) {
            throw new UnauthorizedException(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
