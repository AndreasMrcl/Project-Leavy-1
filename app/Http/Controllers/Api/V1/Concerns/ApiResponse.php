<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function ok(mixed $data = null, array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => array_merge(['server_time' => now()->toIso8601String()], $meta),
        ], $status);
    }

    protected function created(mixed $data = null, array $meta = []): JsonResponse
    {
        return $this->ok($data, $meta, 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    protected function error(string $key, string|array $messages, int $status = 422): JsonResponse
    {
        return response()->json([
            'errors' => [
                $key => is_array($messages) ? $messages : [$messages],
            ],
        ], $status);
    }
}
