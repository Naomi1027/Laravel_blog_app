<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

        $this->renderable(function (Throwable $e, $request) {
            if (! $request->is('api/*') || ! $e instanceof HttpException) {
                return;
            }

            $statusCode = $e->getStatusCode();

            [$title, $detail] = match ($statusCode) {
                403 => ['Forbidden', $e->getMessage() ?: 'Forbidden'],
                404 => ['Not Found', $e->getMessage() ?: 'Not Found'],
                default => ['Error', $e->getMessage() ?: 'An error occurred'],
            };

            return response()->json([
                'title' => $title,
                'status' => $statusCode,
                'detail' => $detail,
            ], $statusCode, [
                'Content-Type' => 'application/problem+json',
            ]);
        });
    }
}
