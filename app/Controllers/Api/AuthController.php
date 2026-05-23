<?php

namespace App\Controllers\Api;

use App\Api\BaseApiController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class AuthController extends BaseApiController
{
    public function login(): ResponseInterface
    {
        $payload = $this->payload();
        $username = trim((string) ($payload['username'] ?? ''));
        $password = (string) ($payload['password'] ?? '');

        if ($username === '' || $password === '') {
            return $this->failure('Identifiants requis.', ResponseInterface::HTTP_BAD_REQUEST, [
                'username' => $username === '' ? 'Le nom utilisateur est requis.' : null,
                'password' => $password === '' ? 'Le mot de passe est requis.' : null,
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->findActiveByUsername($username);

        if ($user === null || ! password_verify($password, (string) $user['mot_de_passe'])) {
            return $this->failure('Identifiants invalides.', ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $userModel->touchLastConnected((int) $user['id_utilisateur']);

        $jwt = service('jwtService');
        $token = $jwt->createTokenPair(
            $user,
            $this->request->getUserAgent()->getAgentString(),
            $this->request->getIPAddress()
        );

        return $this->success([
            'user' => $jwt->publicUser($user),
            'token' => $token,
        ], 'Connexion reussie.');
    }

    public function refresh(): ResponseInterface
    {
        $payload = $this->payload();
        $refreshToken = trim((string) ($payload['refresh_token'] ?? ''));

        if ($refreshToken === '') {
            return $this->failure('Refresh token requis.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $result = service('jwtService')->refresh(
            $refreshToken,
            $this->request->getUserAgent()->getAgentString(),
            $this->request->getIPAddress()
        );

        if ($result === null) {
            return $this->failure('Refresh token invalide ou expire.', ResponseInterface::HTTP_UNAUTHORIZED);
        }

        return $this->success($result, 'Token renouvele.');
    }

    public function logout(): ResponseInterface
    {
        $payload = $this->payload();
        $refreshToken = trim((string) ($payload['refresh_token'] ?? ''));
        $jwt = service('jwtService');

        if ($refreshToken === '') {
            return $this->failure('Refresh token requis.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        if (! $jwt->revokeRefreshToken($refreshToken)) {
            return $this->failure('Refresh token invalide ou deja revoque.', ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $jwt->revokeAccessTokenClaims(service('authContext')->claims());
        service('authContext')->clear();

        return $this->success(null, 'Deconnexion effectuee.');
    }

    public function me(): ResponseInterface
    {
        $context = service('authContext');
        $user = $context->user();
        $claims = $context->claims();

        if ($user === null || $claims === null) {
            return $this->failure('Utilisateur non authentifie.', ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $publicUser = service('jwtService')->publicUser($user);
        $publicUser['claims'] = [
            'sub' => $claims['sub'] ?? null,
            'username' => $claims['username'] ?? null,
            'role' => $claims['role'] ?? null,
            'permissions' => $claims['permissions'] ?? [],
            'iat' => $claims['iat'] ?? null,
            'exp' => $claims['exp'] ?? null,
        ];

        return $this->success($publicUser);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        try {
            $json = $this->request->getJSON(true);
        } catch (Throwable) {
            $json = null;
        }

        if (is_array($json)) {
            return $json;
        }

        return $this->request->getPost();
    }
}
