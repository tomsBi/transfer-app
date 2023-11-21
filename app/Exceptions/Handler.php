<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
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
        $this->renderable(function (CustomException $e, Request $request) {
            return response()->json([
                'timestamp' => now()->toIso8601String(),
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
                'path' => $request->getPathInfo(),
            ], $e->getCode());
        });
    }
}
