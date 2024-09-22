<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
    public function render($request, Throwable $e)
    {
        if ($request->is('api/*')) { // Ensure this only affects API routes
            if ($e instanceof UnauthorizedHttpException) {
                $preException = $e->getPrevious();
                if ($preException instanceof TokenExpiredException) {
                    return response()->json(['error' => 'Token has expired'], 401);
                } else if ($preException instanceof TokenInvalidException) {
                    return response()->json(['error' => 'Token is invalid'], 401);
                } else if ($preException instanceof JWTException) {
                    return response()->json(['error' => $preException->getMessage()], 500);
                }
            }
            if ($e instanceof TokenExpiredException) {
                return response()->json(['error' => 'Token has expired'], 401);
            }
        }

        return parent::render($request, $e);
    }
}
