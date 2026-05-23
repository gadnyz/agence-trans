<?php

namespace App\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseApiController extends ResourceController
{
    protected $format = 'json';

    protected function success(mixed $data = null, string $message = 'OK', int $status = ResponseInterface::HTTP_OK): ResponseInterface
    {
        return $this->respond([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $status);
    }

    protected function failure(string $message, int $status, mixed $errors = null): ResponseInterface
    {
        return $this->respond([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $status);
    }
}
