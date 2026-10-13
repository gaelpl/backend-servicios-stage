<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ApiResponse
{
    public const ERROR_CODES = [
        'VALIDATION_ERROR' => 'Error de validación',
        'NOT_FOUND' => 'Recurso no encontrado',
        'UNAUTHORIZED' => 'No autorizado',
        'FORBIDDEN' => 'Acceso denegado',
        'METHOD_NOT_ALLOWED' => 'Método no permitido',
        'CONFLICT' => 'Conflicto',
        'BUSINESS_RULE' => 'Regla de negocio',
        'INTERNAL_ERROR' => 'Error interno del servidor',
    ];

    public static function requestId(): string
    {
        if (app()->bound('api.request_id')) {
            return app('api.request_id');
        }

        $id = request()->header('X-Request-Id');

        if (! is_string($id) || $id === '') {
            $id = 'req-'.Str::lower(Str::random(12));
        }

        app()->instance('api.request_id', $id);

        return $id;
    }

    public static function timestamp(): string
    {
        return now()->utc()->format('Y-m-d\TH:i:s\Z');
    }

    public static function meta(?array $pagination = null): array
    {
        $meta = [
            'request_id' => self::requestId(),
            'timestamp' => self::timestamp(),
        ];

        if ($pagination !== null) {
            $meta['pagination'] = $pagination;
        }

        return $meta;
    }

    public static function success(mixed $data, int $status = 200, ?array $pagination = null): JsonResponse
    {
        if ($data instanceof \JsonSerializable) {
            $data = $data->jsonSerialize();
        }

        return response()->json([
            'data' => $data,
            'meta' => self::meta($pagination),
        ], $status);
    }

    public static function error(
        string $code,
        ?string $message = null,
        array $details = [],
        int $status = 400,
        ?string $codeName = null,
    ): JsonResponse {
        return response()->json([
            'error' => [
                'code' => $code,
                'code_name' => $codeName ?? (self::ERROR_CODES[$code] ?? 'Error'),
                'message' => $message ?? (self::ERROR_CODES[$code] ?? 'Error'),
                'details' => $details,
            ],
            'meta' => self::meta(),
        ], $status);
    }

    public static function pagination(LengthAwarePaginator $paginator): array
    {
        return [
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'total_pages' => $paginator->lastPage(),
        ];
    }
}
