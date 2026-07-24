<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $requiredPermissions = $this->normalizeArguments($arguments);

        if ($requiredPermissions === []) {
            return null;
        }

        $context = service('authContext');

        if ($context->user() === null) {
            return service('response')
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON([
                    'success' => false,
                    'message' => 'Authentification requise.',
                    'data' => null,
                    'errors' => null,
                ]);
        }

        foreach ($requiredPermissions as $permission) {
            if ($context->hasPermission($permission)) {
                return null;
            }
        }

        return service('response')
            ->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
            ->setJSON([
                'success' => false,
                'message' => 'Permission insuffisante.',
                'data' => null,
                'errors' => [
                    'required' => $requiredPermissions,
                ],
            ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    /**
     * @return list<string>
     */
    private function normalizeArguments($arguments): array
    {
        if (! is_array($arguments)) {
            return [];
        }

        $permissions = [];

        foreach ($arguments as $argument) {
            foreach (explode(',', (string) $argument) as $permission) {
                $permission = trim($permission);

                if ($permission !== '') {
                    $permissions[] = $permission;
                }
            }
        }

        return array_values(array_unique($permissions));
    }
}
