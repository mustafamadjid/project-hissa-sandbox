<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

abstract class Controller
{
    protected function forbidden(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }

    protected function validationError(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal.',
            'errors' => $exception->errors(),
        ], 422);
    }

    protected function serverError(string $message, ?Throwable $exception = null): JsonResponse
    {
        if ($exception !== null) {
            report($exception);
        }

        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }
}
