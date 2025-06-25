<?php

declare(strict_types=1);

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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception) {
            return response()
                ->json([
                    'title' => 'The given data was invalid.',
                    'status' => $exception->status,
                    'errors' => $exception->errors(),
                ],
                    $exception->status,
                    ['Content-Type' => 'application/problem+json']
                );
        });

        $exceptions->render(function (NotFoundHttpException $exception) {
            return response()
                ->json([
                    'title' => 'Resource not found.',
                    'status' => $exception->getStatusCode(),
                    'detail' => 'The requested resource was not found.',
                ],
                    $exception->getStatusCode(),
                    ['Content-Type' => 'application/problem+json']
                );
        });
    })->create();
