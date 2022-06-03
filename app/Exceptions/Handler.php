<?php

namespace App\Exceptions;

use App\Enums\Codes;
use App\Enums\ResponseCodes;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Octane\Exceptions\DdException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseTrait;

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
        $this->reportable(function (Throwable $exception) {
        });
    }

    public function render($request, Throwable $e)
    {
        $code = ResponseCodes::E1001;
        $errors = null;
        if (
            $e instanceof UnauthorizedException
            || $e instanceof AuthorizationException
        ) {
            $code = ResponseCodes::E2004;
        } elseif (
            $e instanceof AuthenticationException
        ) {
            $code = ResponseCodes::E2007;
        } elseif (
            $e instanceof NotFoundHttpException
            || $e instanceof ModelNotFoundException
            || $e instanceof RelationNotFoundException
        ) {
            $code = ResponseCodes::E1008;
        } elseif ($e instanceof ValidationException) {
            $code = ResponseCodes::E1002;
            $errors = $e->errors();
        } elseif ($e instanceof TooManyRequestsHttpException) {
            $code = ResponseCodes::E1015;
        }

        // White log exception
        if (!($e instanceof AuthenticationException)) {
            Log::error($e);
        }

        if ($e instanceof DdException) {
            return parent::render($request, $e);
        }

        return $request->wantsJson()
            ? $this->response($code, null, $errors, [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->map(function ($trace) {
                    return Arr::except($trace, ['args']);
                })->all(),
            ])
            : parent::render($request, $e);
    }
}
