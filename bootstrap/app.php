<?php

declare(strict_types=1);

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: '',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception) {
            return response()
                ->json([
                    'title' => 'The given data was invalid.',
                    'status' => 422,
                    'errors' => $exception->errors(),
                ],
                    422,
                    ['Content-Type' => 'application/problem+json']
                );
        });

        $exceptions->render(function (NotFoundHttpException $exception) {
            return response()
                ->json([
                    'title' => 'Resource not found.',
                    'status' => 404,
                    'detail' => 'The requested resource was not found.',
                ],
                    404,
                    ['Content-Type' => 'application/problem+json']
                );
        });

        $exceptions->render(function (AuthenticationException $exception) {
            return response()
                ->json([
                    'title' => $exception->getMessage(),
                    'status' => 401,
                    'detail' => 'Authentication is required to access this resource.',
                ], 401, ['Content-Type' => 'application/problem+json']);
        });
    })->create();
