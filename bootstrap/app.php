<?php

use App\Http\Middleware\SetRequestLocale;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'jt-express-webhook',
            '/api/jt-express-webhook',
              '/bosta/webhook',
            '/api/bosta/webhook',
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'protect.docs' => \App\Http\Middleware\ProtectDocs::class,
        ]);
        
        $middleware->prependToGroup('api', \App\Http\Middleware\AlwaysAcceptJson::class);
        $middleware->appendToGroup('api', \Illuminate\Session\Middleware\StartSession::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\ApiKeyMiddleware::class);
        $middleware->appendToGroup('api', HandleCors::class);
        $middleware->appendToGroup('api', SetRequestLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (NotFoundHttpException $e) {
            return response()->json(['message' => 'Page not found'], 404);
        });

        $exceptions->renderable(function (MethodNotAllowedHttpException $e) {
            return response()->json([
                'message' => 'The method is not allowed for this endpoint.',
            ], 405);
        });

         $exceptions->renderable(function (ModelNotFoundException $e) {
             return response()->json(['message' => 'Object not found.'], 404);
         });
    })->create();
