<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

/**
 * Auth pour les endpoints JSON : Bearer JWT (clients externes) OU session PHP (UI monolithique).
 */
class JwtAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authorization = $request->getHeaderLine('Authorization');

        if (preg_match('/^Bearer\s+(\S+)$/i', $authorization, $matches)) {
            $result = $this->authenticateFromToken(trim($matches[1]));

            // Token expiré/invalide : retomber sur la session UI si disponible
            if ($result !== null && session()->get('user')) {
                return $this->authenticateFromSession();
            }

            return $result;
        }

        return $this->authenticateFromSession();
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    private function authenticateFromToken(string $token): ?ResponseInterface
    {
        try {
            $claims = service('jwtService')->decodeAccessToken($token);
        } catch (Throwable) {
            return $this->unauthorized('Session expiree. Veuillez vous reconnecter.');
        }

        return $this->bindUser((int) ($claims['sub'] ?? 0), $claims);
    }

    private function authenticateFromSession(): ?ResponseInterface
    {
        $sessionUser = session()->get('user');

        if (! is_array($sessionUser)) {
            return $this->unauthorized('Session requise. Veuillez vous reconnecter.');
        }

        $userId = (int) ($sessionUser['id'] ?? $sessionUser['id_utilisateur'] ?? $sessionUser['claims']['sub'] ?? 0);

        if ($userId <= 0) {
            return $this->unauthorized('Session invalide. Veuillez vous reconnecter.');
        }

        $token = (string) session()->get('access_token');
        $claims = [];

        if ($token !== '') {
            try {
                $claims = service('jwtService')->decodeAccessToken($token);
            } catch (Throwable) {
                // Session UI valide même si le JWT a expiré : on reconstruit le contexte depuis la DB.
                $claims = [];
            }
        }

        return $this->bindUser($userId, $claims);
    }

    /**
     * @param array<string, mixed> $claims
     */
    private function bindUser(int $userId, array $claims): ?ResponseInterface
    {
        $user = (new UserModel())->findActiveById($userId);

        if ($user === null) {
            return $this->unauthorized('Compte utilisateur indisponible.');
        }

        $claims['sub'] = $userId;
        $claims['role'] = $user['code_role'] ?? ($claims['role'] ?? null);
        $claims['permissions'] = service('jwtService')->permissionsForUser($user);

        service('authContext')->set($user, $claims);

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
