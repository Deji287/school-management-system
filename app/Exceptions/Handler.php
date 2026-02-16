<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    public function register(): void
    {
        //
    }

    public function render($request, Throwable $exception)
    {
        // Only handle API routes
        if ($request->is('api/*')) {
            $status = 500;

            // If the exception is an HTTP exception, get the proper status code
            if ($exception instanceof HttpException) {
                $status = $exception->getStatusCode();
            }

            // Handle 404 specifically
            if ($exception instanceof NotFoundHttpException) {
                $status = 404;
                $message = 'API route not found';
            } else {
                $message = $exception->getMessage() ?: 'Server Error';
            }

            return response()->json([
                'success' => false,
                'message' => $message,
            ], $status);
        }

        // Fallback to default HTML response for web routes
        return parent::render($request, $exception);
    }
}
