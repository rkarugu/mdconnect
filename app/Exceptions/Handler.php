<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request; // Added for type hinting
use Illuminate\Http\JsonResponse; // Added for type hinting
use Illuminate\Validation\ValidationException; // Added for type hinting
use Illuminate\Support\Arr; // Added for Arr::except
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
                    ? response()->json(['message' => $exception->getMessage()], 401)
                    : redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Ensure JSON responses for API routes
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return $this->prepareJsonResponse($request, $e);
            }
        });
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  Request  $request
     * @param  Throwable  $e
     * @return JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $e): JsonResponse
    {
        $statusCode = $this->isHttpException($e) ? $e->getStatusCode() : 500;
        $response = [
            'message' => $e->getMessage() ?: 'Server Error',
        ];

        if ($e instanceof ValidationException) {
            $response['errors'] = $e->errors();
            $statusCode = $e->status;
        } elseif (config('app.debug')) {
            $response['exception'] = get_class($e);
            $response['file'] = $e->getFile();
            $response['line'] = $e->getLine();
            // $response['trace'] = collect($e->getTrace())->map(function ($trace) {
            //     return Arr::except($trace, ['args']);
            // })->all(); // Temporarily commented out for stability
        }

        return response()->json($response, $statusCode);
    }
}
