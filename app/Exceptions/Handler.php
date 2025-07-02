<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Handle API requests with custom error responses
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle API exceptions with custom error responses.
     */
    public function handleApiException($request, Throwable $exception): JsonResponse
    {
        $statusCode = 500;
        $message = 'Internal Server Error';
        $errors = null;

        switch (true) {
            case $exception instanceof AuthenticationException:
                $statusCode = 401;
                $message = 'You are not authenticated to access this resource.';
                break;

            case $exception instanceof UnauthorizedHttpException:
                $statusCode = 403;
                $message = 'You are not authorized to access this resource.';
                break;

            case $exception instanceof MethodNotAllowedHttpException:
                $statusCode = 405;
                $message = 'The requested HTTP method is not supported for this endpoint.';
                $errors = [
                    'method' => $request->method(),
                    'supported_methods' => $exception->getHeaders()['Allow'] ?? []
                ];
                break;

            case $exception instanceof NotFoundHttpException:
                $statusCode = 404;
                $message = 'The requested resource was not found.';
                break;

            case $exception instanceof ModelNotFoundException:
                $statusCode = 404;
                $message = 'The requested resource was not found.';
                break;

            case $exception instanceof ValidationException:
                $statusCode = 422;
                $message = 'The given data was invalid.';
                $errors = $exception->errors();
                break;

            default:
                // For other exceptions, use a generic error message in production
                if (config('app.debug')) {
                    $message = $exception->getMessage();
                }
                break;
        }

        $response = [
            'success' => false,
            'message' => $message,
            'status_code' => $statusCode,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        if (config('app.debug')) {
            $response['debug'] = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        return response()->json($response, $statusCode);
    }
} 