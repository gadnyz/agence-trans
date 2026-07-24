<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Table de redirection par rôle — chaque rôle a sa page d'accueil.
     */
    private array $roleHome = [
        'super_admin' => '/super-admin/dashboard',
        'admin'       => '/admin/dashboard',
        'recept'      => '/recept/reservations',
        'driver'      => '/driver/planning',
    ];

    /**
     * Exécuté avant le contrôleur.
     * Vérifie si le rôle de l'utilisateur correspond aux rôles autorisés dans la route.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('user');

        // 1. Si pas d'utilisateur en session, rediriger vers login
        if (!$user) {
            return redirect()->to('/');
        }

        // 2. Récupérer le code du rôle
        $userRole = $user['role']['code'] ?? '';

        // 3. Si le rôle est autorisé, laisser passer
        if (!empty($arguments) && in_array($userRole, $arguments)) {
            return null;
        }

        // 4. Rôle insuffisant : rediriger vers la page d'accueil du rôle de l'utilisateur
        $home = $this->roleHome[$userRole] ?? '/';

        return redirect()->to($home)->with('error', 'Accès refusé : vous n\'avez pas la permission d\'accéder à cette page.');
    }

    /**
     * Exécuté après le contrôleur.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à exécuter après la requête pour ce filtre
    }
}