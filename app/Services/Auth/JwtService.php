<?php

namespace App\Services\Auth;

use App\Models\AuthRefreshTokenModel;
use App\Models\AuthRevokedAccessTokenModel;
use App\Models\UserModel;
use Config\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;

class JwtService
{
    private Auth $config;
    private AuthRefreshTokenModel $refreshTokens;
    private AuthRevokedAccessTokenModel $revokedAccessTokens;
    private UserModel $users;

    public function __construct(
        ?Auth $config = null,
        ?AuthRefreshTokenModel $refreshTokens = null,
        ?AuthRevokedAccessTokenModel $revokedAccessTokens = null,
        ?UserModel $users = null,
    ) {
        $this->config = $config ?? config('Auth');
        $this->refreshTokens = $refreshTokens ?? new AuthRefreshTokenModel();
        $this->revokedAccessTokens = $revokedAccessTokens ?? new AuthRevokedAccessTokenModel();
        $this->users = $users ?? new UserModel();
    }

    /**
     * @param array<string, mixed> $user
     *
     * @return array<string, mixed>
     */
    public function createTokenPair(array $user, ?string $userAgent = null, ?string $ipAddress = null): array
    {
        $issuedAt = time();
        $accessExpiresAt = $issuedAt + $this->config->accessTokenTtl;
        $refreshExpiresAt = $issuedAt + $this->config->refreshTokenTtl;
        $refreshToken = $this->randomToken();

        $this->refreshTokens->insert([
            'id_utilisateur' => (int) $user['id_utilisateur'],
            'token_hash' => $this->hashRefreshToken($refreshToken),
            'issued_at' => date('Y-m-d H:i:s', $issuedAt),
            'expires_at' => date('Y-m-d H:i:s', $refreshExpiresAt),
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress,
        ]);

        return [
            'access_token' => $this->createAccessToken($user, $issuedAt, $accessExpiresAt),
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $this->config->accessTokenTtl,
            'expires_at' => date(DATE_ATOM, $accessExpiresAt),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function decodeAccessToken(string $token): array
    {
        $claims = (array) JWT::decode($token, new Key($this->config->jwtSecret, $this->config->jwtAlgorithm));

        if (($claims['token_type'] ?? null) !== 'access') {
            throw new RuntimeException('Invalid token type.');
        }

        if (($claims['iss'] ?? null) !== $this->config->jwtIssuer) {
            throw new RuntimeException('Invalid token issuer.');
        }

        if (($claims['aud'] ?? null) !== $this->config->jwtAudience) {
            throw new RuntimeException('Invalid token audience.');
        }

        if (! isset($claims['jti']) || $this->revokedAccessTokens->isRevoked((string) $claims['jti'])) {
            throw new RuntimeException('Token revoked.');
        }

        $claims['permissions'] = isset($claims['permissions'])
            ? (array) $claims['permissions']
            : [];

        return $claims;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function refresh(string $refreshToken, ?string $userAgent = null, ?string $ipAddress = null): ?array
    {
        $row = $this->refreshTokens->findActiveByHash($this->hashRefreshToken($refreshToken));

        if ($row === null) {
            return null;
        }

        $user = $this->users->findActiveById((int) $row['id_utilisateur']);

        if ($user === null) {
            return null;
        }

        $this->refreshTokens->revoke((int) $row['id_refresh_token']);

        return [
            'user' => $this->publicUser($user),
            'token' => $this->createTokenPair($user, $userAgent, $ipAddress),
        ];
    }

    public function revokeRefreshToken(string $refreshToken): bool
    {
        return $this->refreshTokens->revokeByHash($this->hashRefreshToken($refreshToken));
    }

    /**
     * @param array<string, mixed>|null $claims
     */
    public function revokeAccessTokenClaims(?array $claims): bool
    {
        if (
            $claims === null
            || empty($claims['jti'])
            || empty($claims['sub'])
            || empty($claims['exp'])
        ) {
            return false;
        }

        return $this->revokedAccessTokens->revoke(
            (string) $claims['jti'],
            (int) $claims['sub'],
            (int) $claims['exp']
        );
    }

    /**
     * @param array<string, mixed> $user
     *
     * @return list<string>
     */
    public function permissionsForUser(array $user): array
    {
        return $this->permissionsForRole((string) ($user['code_role'] ?? ''));
    }

    /**
     * @return list<string>
     */
    public function permissionsForRole(string $role): array
    {
        return $this->config->rolePermissions[$role] ?? [];
    }

    /**
     * @param array<string, mixed> $user
     *
     * @return array<string, mixed>
     */
    public function publicUser(array $user): array
    {
        return [
            'id' => (int) $user['id_utilisateur'],
            'nom' => $user['nom'],
            'postnom' => $user['postnom'],
            'prenom' => $user['prenom'],
            'username' => $user['username'],
            'telephone' => $user['telephone'],
            'email' => $user['email'],
            'role' => [
                'code' => $user['code_role'] ?? null,
                'libelle' => $user['role_libelle'] ?? null,
            ],
            'permissions' => $this->permissionsForUser($user),
        ];
    }

    /**
     * @param array<string, mixed> $user
     */
    private function createAccessToken(array $user, int $issuedAt, int $expiresAt): string
    {
        $payload = [
            'iss' => $this->config->jwtIssuer,
            'aud' => $this->config->jwtAudience,
            'iat' => $issuedAt,
            'nbf' => $issuedAt,
            'exp' => $expiresAt,
            'jti' => $this->randomToken(16),
            'token_type' => 'access',
            'sub' => (string) $user['id_utilisateur'],
            'username' => $user['username'],
            'role' => $user['code_role'] ?? null,
            'permissions' => $this->permissionsForUser($user),
        ];

        return JWT::encode($payload, $this->config->jwtSecret, $this->config->jwtAlgorithm);
    }

    private function randomToken(int $bytes = 64): string
    {
        return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
    }

    private function hashRefreshToken(string $refreshToken): string
    {
        return hash('sha256', $refreshToken);
    }
}
