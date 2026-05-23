<?php

namespace App\Api;

class ApiResponseService
{
    /**
     * @return array<string, mixed>
     */
    public function success(mixed $data = null, string $message = 'OK'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function failure(string $message, mixed $errors = null): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ];
    }
}
