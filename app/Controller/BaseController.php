<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController
{
    protected function toJsonResponse(array $response, int $status = 200): JsonResponse
    {
        return new JsonResponse($response, $status);
    }

    protected function handleException(\Throwable $e): JsonResponse
    {
        $code = $e instanceof \Domain\Exception\ValidationException ? 422 :
                ($e instanceof \Domain\Exception\EntityNotFoundException ? 404 : 500);

        return $this->toJsonResponse([
            'error' => $e->getMessage(),
            'type' => (new \ReflectionClass($e))->getShortName(),
        ], $code);
    }
}
