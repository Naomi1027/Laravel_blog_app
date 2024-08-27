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
            if ($request->is('api/*')) {
                $title = '';
                $detail = '';

                if ($e instanceof HttpException) {
                    $cast = fn ($orig): HttpException => $orig;  // HttpException へ型変換
                    $httpEx = $cast($e);
                    switch ($httpEx->getStatusCode()) {
                        case 403:
                            $title = __('Forbidden');
                            $detail = __($httpEx->getMessage() ?: 'Forbidden');
                            break;
                        default:
                            return;
                    }

                    return response()->json([
                        'title' => $title,
                        'status' => $httpEx->getStatusCode(),
                        'detail' => $detail,
                    ], $httpEx->getStatusCode(), [
                        'Content-Type' => 'application/problem+json',
                    ]);
                }
            }
        });

    }
}
