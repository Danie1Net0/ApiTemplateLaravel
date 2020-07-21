<?php

namespace App\Exceptions;

use App\Http\Resources\Validations\MessageResponseResource;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return JsonResponse|object
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof DeleteResourceException)
            return new MessageResponseResource(['success' => false, 'message' => $exception->getMessage()]);

        if ($exception instanceof ValidationException)
            return (new MessageResponseResource(['success' => false, 'message' => collect($exception->errors())->first()]))
                ->response()
                ->setStatusCode($exception->status);

        if ($exception instanceof ModelNotFoundException)
            return (new MessageResponseResource(['success' => false, 'message' => $exception->getMessage()]))
                ->response()
                ->setStatusCode(Response::HTTP_NOT_FOUND);

        if ($exception instanceof NotFoundHttpException)
            return (new MessageResponseResource(['success' => false, 'message' => 'Rota não encontrada.']))
                ->response()
                ->setStatusCode(Response::HTTP_NOT_FOUND);

        if ($exception instanceof UnauthorizedException)
            return (new MessageResponseResource(['success' => false, 'message' => 'O usuário não tem permissões para realizar essa operação.']))
                ->response()
                ->setStatusCode(Response::HTTP_FORBIDDEN);

        return parent::render($request, $exception);
    }
}
