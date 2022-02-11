<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
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
    public function register()
    {
        // $this->reportable(function (Throwable $e) {
        //     //
        // });

        $this->renderable(function (TokenInvalidException $e, $request) {
            return Response::json([
                'error' => true,
                'message' => 'Invalid token'
            ], 403);
        });
        $this->renderable(function (TokenExpiredException $e, $request) {
            return Response::json([
                'error' => true,
                'message' => 'Token has Expired'
            ], 403);
        });

        $this->renderable(function (JWTException $e, $request) {
            return Response::json([
                'error' => true,
                'message' => "Please provide a valid token"
            ], 403);
        });
    }
}
