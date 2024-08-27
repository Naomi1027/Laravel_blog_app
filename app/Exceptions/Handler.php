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
                if ($e instanceof HttpException) {
                    switch ($e->getStatusCode()) {
                        case 403:
                            $title = __('Forbidden');
                            $detail = __($e->getMessage() ?: 'Forbidden');
                            break;
                        case 404:
                            $title = __('Not Found');
                            $detail = __($e->getMessage() ?: 'Not Found');
                            break;
                        default:
                            return;
                    }

                    return response()->json([
                        'title' => $title,
                        'status' => $e->getStatusCode(),
                        'detail' => $detail,
                    ], $e->getStatusCode(), [
                        'Content-Type' => 'application/problem+json',
                    ]);
                }
            }
        });
    }
}
