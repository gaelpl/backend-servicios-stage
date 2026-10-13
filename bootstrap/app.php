<?php

use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $isApi = fn (Request $request) => $request->is('api/*') || $request->expectsJson();

        $exceptions->render(function (ValidationException $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiResponse::error('VALIDATION_ERROR', $e->getMessage(), $e->errors(), 422);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiResponse::error('NOT_FOUND', 'El recurso solicitado no existe.', [], 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiResponse::error('NOT_FOUND', 'El recurso solicitado no existe.', [], 404);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiResponse::error('UNAUTHORIZED', 'Debe autenticarse para continuar.', [], 401);
            }
        });

        $exceptions->render(function (AuthorizationException|AccessDeniedHttpException $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiResponse::error('FORBIDDEN', 'No tiene permisos para realizar esta acción.', [], 403);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiResponse::error('METHOD_NOT_ALLOWED', 'Método HTTP no permitido para esta ruta.', [], 405);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiResponse::error(
                    'INTERNAL_ERROR',
                    $e->getMessage() ?: 'Ocurrió un error al procesar la solicitud.',
                    [],
                    $e->getStatusCode(),
                );
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return ApiResponse::error(
                    'INTERNAL_ERROR',
                    config('app.debug') ? $e->getMessage() : 'Ocurrió un error interno en el servidor.',
                    [],
                    500,
                );
            }
        });
    })->create();
