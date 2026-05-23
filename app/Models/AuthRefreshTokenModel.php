<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthRefreshTokenModel extends Model
{
    protected $table = 'auth_refresh_tokens';
    protected $primaryKey = 'id_refresh_token';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps = false;
    protected $allowedFields = [
        'id_utilisateur',
        'token_hash',
        'issued_at',
        'expires_at',
        'revoked_at',
        'user_agent',
        'ip_address',
        'deleted_at',
    ];

    /**
     * @return array<string, mixed>|null
     */
    public function findActiveByHash(string $hash): ?array
    {
        return $this->where('token_hash', $hash)
            ->where('revoked_at', null)
            ->where('deleted_at', null)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();
    }

    public function revoke(int $id): bool
    {
        return $this->update($id, [
            'revoked_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function revokeByHash(string $hash): bool
    {
        $row = $this->where('token_hash', $hash)
            ->where('revoked_at', null)
            ->where('deleted_at', null)
            ->first();

        if ($row === null) {
            return false;
        }

        return $this->revoke((int) $row['id_refresh_token']);
    }
}
