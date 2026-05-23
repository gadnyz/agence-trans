<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthRevokedAccessTokenModel extends Model
{
    protected $table = 'auth_revoked_access_tokens';
    protected $primaryKey = 'id_revoked_access_token';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps = false;
    protected $allowedFields = [
        'id_utilisateur',
        'jti',
        'expires_at',
        'revoked_at',
        'deleted_at',
    ];

    public function isRevoked(string $jti): bool
    {
        return $this->where('jti', $jti)
            ->where('deleted_at', null)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->countAllResults() > 0;
    }

    public function revoke(string $jti, int $userId, int $expiresAt): bool
    {
        if ($this->isRevoked($jti)) {
            return true;
        }

        return (bool) $this->insert([
            'id_utilisateur' => $userId,
            'jti' => $jti,
            'expires_at' => date('Y-m-d H:i:s', $expiresAt),
            'revoked_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
