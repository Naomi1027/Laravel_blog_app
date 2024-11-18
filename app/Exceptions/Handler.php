<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

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

        $this->renderable(function (HttpException $e, Request $request) {
            if ($request->is('api/*')) {
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
            }
        });
    }

    protected function convertValidationExceptionToResponse($e, $request)
    {
        $url = $exception->redirectTo ?? url()->previous();

        $inputs = $request->all();
        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $inputs[$key] = $this->removeFilesFromInput($value);
            }

            if ($value instanceof SymfonyUploadedFile) {
                // 元ファイル名だけ返す
                $inputs[$key] = basename($value->getClientOriginalName());
            }
        }

        $e->response = redirect($url)
            ->withInput($inputs)
            ->withErrors(
                $e->errors(),
                $e->errorBag
            );

        return parent::convertValidationExceptionToResponse($e, $request);
    }

}
