<?php

namespace App\Exceptions;

use Throwable;
use ParseError;
use ArgumentCountError;
use App\Traits\ApiResponse;
use BadMethodCallException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $exception);
        }
        return parent::render($request, $exception);
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function handleApiException($request, Throwable $exception)
    {
        $this->withException($exception);

        if ($exception instanceof ValidationException) {
            return $this->respondValidationErrors($exception);
        }

        if ($exception instanceof AuthorizationException) {
            return $this->respondUnAuthorized();
        }

        if (
            $exception instanceof ModelNotFoundException ||
            $exception instanceof NotFoundHttpException
        ) {
            return $this->respondNotFound();
        }

        if ($exception instanceof AuthenticationException) {
            return $this->apiResponseError([
                'message' => $exception->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof QueryException) {
            return $this->apiResponseError([
                'message' => 'There was Issue with the Query',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($exception instanceof ParseError) {
            return $this->apiResponseError([
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($exception instanceof BindingResolutionException) {
            return $this->apiResponseError([
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($exception instanceof BadMethodCallException) {
            return $this->apiResponseError([
                'message' => $exception->getMessage()
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->apiResponseError([
                'message' => $exception->getMessage()
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        if ($exception instanceof ArgumentCountError) {
            return $this->apiResponseError([
                'message' => 'Too few arguments to function'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($exception instanceof OAuthServerException) {
            return $this->respondUnAuthorized();
        }

        if ($exception instanceof ThrottleRequestsException) {
            return $this->apiResponseError([
                'message' => 'Too Many Requests'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        if ($exception instanceof PostTooLargeException) {
            return $this->apiResponseError([
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($exception instanceof RouteNotFoundException) {
            return $this->respondNotFound();
        }

        if ($exception instanceof HttpException) {
            return $this->respondForbidden();
        }

        return $this->respondInternalError($exception);
    }
}
