<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig
{
    public string $jwtSecret = '';
    public string $jwtAlgorithm = 'HS256';
    public string $jwtIssuer = 'kashala-trans-api';
    public string $jwtAudience = 'kashala-trans-client';
    public int $accessTokenTtl = 900;
    public int $refreshTokenTtl = 604800;
    public string $permissionsFile = APPPATH . 'Config/permissions.json';

    /**
     * @var array<string, list<string>>
     */
    public array $rolePermissions = [
        'super_admin' => ['*'],
        'admin' => [
            'users.read',
            'users.manage',
            'clients.manage',
            'fleet.manage',
            'routes.manage',
            'planning.manage',
            'reservations.manage',
            'payments.manage',
            'reports.read',
            'settings.manage',
        ],
        'recept' => [
            'clients.manage',
            'routes.read',
            'planning.read',
            'reservations.manage',
            'payments.manage',
        ],
        'driver' => [
            'planning.read',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->jwtSecret = (string) env('jwt.secret', '');
        $this->jwtAlgorithm = (string) env('jwt.algorithm', $this->jwtAlgorithm);
        $this->jwtIssuer = (string) env('jwt.issuer', $this->jwtIssuer);
        $this->jwtAudience = (string) env('jwt.audience', $this->jwtAudience);
        $this->accessTokenTtl = (int) env('jwt.accessTokenTtl', $this->accessTokenTtl);
        $this->refreshTokenTtl = (int) env('jwt.refreshTokenTtl', $this->refreshTokenTtl);

        if ($this->jwtSecret === '') {
            $this->jwtSecret = 'change-this-development-secret-before-production';
        }

        $this->loadRolePermissionsFromFile();
    }

    private function loadRolePermissionsFromFile(): void
    {
        if (! is_file($this->permissionsFile)) {
            return;
        }

        $contents = file_get_contents($this->permissionsFile);

        if ($contents === false) {
            return;
        }

        $config = json_decode($contents, true);

        if (! is_array($config) || ! isset($config['roles']) || ! is_array($config['roles'])) {
            return;
        }

        $rolePermissions = [];

        foreach ($config['roles'] as $role => $permissions) {
            if (! is_string($role) || ! is_array($permissions)) {
                continue;
            }

            $rolePermissions[$role] = array_values(array_filter(
                $permissions,
                static fn ($permission): bool => is_string($permission) && $permission !== ''
            ));
        }

        if ($rolePermissions !== []) {
            $this->rolePermissions = $rolePermissions;
        }
    }
}
