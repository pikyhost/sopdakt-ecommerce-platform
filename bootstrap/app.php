<?php

use App\Http\Middleware\SetRequestLocale;
use App\Http\Middleware\MergeGuestCart;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // CSRF exceptions for specific webhook routes
        $middleware->validateCsrfTokens(except: [
            'jt-express-webhook',
            '/api/jt-express-webhook',
            '/bosta/webhook',
            '/api/bosta/webhook',
        ]);

        // API middleware configuration
        $middleware->api(prepend: [
//            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            HandleCors::class,
        ]);

        // Middleware aliases
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'protect.docs' => \App\Http\Middleware\ProtectDocs::class,
            'localize' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
//            'merge.guest.cart' => MergeGuestCart::class,
        ]);

//        // Add middleware to both web and api groups
//        $middleware->appendToGroup('web', MergeGuestCart::class);
//        $middleware->appendToGroup('api', MergeGuestCart::class);

        // API middleware group
        $middleware->prependToGroup('api', \App\Http\Middleware\AlwaysAcceptJson::class);
        $middleware->appendToGroup('api', SetRequestLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Validation exceptions
        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // General API exceptions
        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return match (true) {
                    $e instanceof NotFoundHttpException => response()->json([
                        'status' => 'error',
                        'message' => 'Endpoint not found.',
                    ], 404),
                    $e instanceof MethodNotAllowedHttpException => response()->json([
                        'status' => 'error',
                        'message' => 'HTTP method not allowed.',
                    ], 405),
                    $e instanceof ModelNotFoundException => response()->json([
                        'status' => 'error',
                        'message' => 'Resource not found.',
                    ], 404),
                    default => response()->json([
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ], method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500),
                };
            }
        });
    })
    ->create();
