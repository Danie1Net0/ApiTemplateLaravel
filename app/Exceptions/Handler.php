<?php

namespace App\Exceptions;

use App\Http\Resources\Shared\MessageResponseResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (DeleteResourceException $exception) {
            return (new MessageResponseResource($exception->getMessage()))
                ->response()
                ->setStatusCode(Response::HTTP_FORBIDDEN);;
        });

        $this->renderable(function (ValidationException $exception) {
            return (new MessageResponseResource(collect(collect($exception->errors())->first())->first()))
                ->response()
                ->setStatusCode($exception->status);
        });

        $this->renderable(function (ModelNotFoundException $exception) {
            return (new MessageResponseResource($exception->getMessage()))
                ->response()
                ->setStatusCode(Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (NotFoundHttpException $exception) {
            return (new MessageResponseResource('Rota não encontrada.'))
                ->response()
                ->setStatusCode(Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (UnauthorizedException $exception) {
            return (new MessageResponseResource('O usuário não tem permissões para realizar essa operação.'))
                ->response()
                ->setStatusCode(Response::HTTP_FORBIDDEN);
        });

        $this->renderable(function (AccessDeniedHttpException $exception) {
            return (new MessageResponseResource('O usuário não tem permissões para realizar essa operação.'))
                ->response()
                ->setStatusCode(Response::HTTP_FORBIDDEN);
        });

        $this->renderable(function (AuthorizationException $exception) {
            return (new MessageResponseResource($exception->getMessage()))
                ->response()
                ->setStatusCode(Response::HTTP_FORBIDDEN);
        });
    }
}
