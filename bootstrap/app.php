<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Route not found
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            /*
             * Como NotFoundHttpException se ejecuta igual si
             * fue lanzado ModelNotFoundException se utiliza $e->getPrevious()
            */
            $previous = $e->getPrevious();

            if ($previous instanceof ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => "model not found"
                ], 404);
            }

            // Regular route not found
            return response()->json([
                'success' => false,
                'message' => 'Endpoint not found'
            ], 404);
        });
    })->create();
