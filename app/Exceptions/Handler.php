<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


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
        $this->renderable(function (ModelNotFoundException|NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                $message = 'No data found.';
                if ($e instanceof ModelNotFoundException) {
                    $message = class_basename($e->getModel()) .' not found';
                }
                return response()->json([
                    'status' => false,
                    'message' => __($message),
                ], 404);
            }
        });
    }
}
