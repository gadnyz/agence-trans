<?php

namespace App\Services\Auth;

class AuthContext
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $user = null;

    /**
     * @var array<string, mixed>|null
     */
    private ?array $claims = null;

    /**
     * @param array<string, mixed> $user
     * @param array<string, mixed> $claims
     */
    public function set(array $user, array $claims): void
    {
        $this->user = $user;
        $this->claims = $claims;
    }

    public function clear(): void
    {
        $this->user = null;
        $this->claims = null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function user(): ?array
    {
        return $this->user;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function claims(): ?array
    {
        return $this->claims;
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->claims['permissions'] ?? [];

        if (! is_array($permissions)) {
            return false;
        }

        return in_array('*', $permissions, true)
            || in_array($permission, $permissions, true);
    }
}
