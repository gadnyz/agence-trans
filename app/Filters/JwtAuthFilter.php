<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class JwtAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authorization = $request->getHeaderLine('Authorization');

        if (! preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
            return $this->unauthorized('Session requise. Veuillez vous reconnecter.');
        }

        try {
            $claims = service('jwtService')->decodeAccessToken(trim($matches[1]));
        } catch (Throwable) {
            return $this->unauthorized('Session expiree. Veuillez vous reconnecter.');
        }

        $userId = (int) ($claims['sub'] ?? 0);
        $user = (new UserModel())->findActiveById($userId);

        if ($user === null) {
            return $this->unauthorized('Compte utilisateur indisponible.');
        }

        $claims['role'] = $user['code_role'] ?? null;
        $claims['permissions'] = service('jwtService')->permissionsForUser($user);

        service('authContext')->set($user, $claims);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    private function unauthorized(string $message): ResponseInterface
    {
        return service('response')
            ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
            ->setJSON([
                'success' => false,
                'message' => $message,
                'data' => null,
                'errors' => null,
            ]);
    }
}
