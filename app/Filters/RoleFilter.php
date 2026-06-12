<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Exécuté avant le contrôleur.
     * Vérifie si le rôle de l'utilisateur correspond aux rôles autorisés dans la route.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('user');

        // 1. Si pas d'utilisateur, le AuthFilter s'en occupera, mais par sécurité :
        if (!$user) {
            return redirect()->to('/');
        }

        // 2. Récupérer le code du rôle (comme dans ton AuthController)
        $userRole = $user['role']['code'] ?? '';

        // 3. Vérifier si le rôle de l'utilisateur est dans les arguments de la route
        // Exemple : ['super_admin', 'admin']
        if (empty($arguments) || !in_array($userRole, $arguments)) {
            
            return redirect()->to('/logout')->with('error', 'Accès refusé : Rôle insuffisant.');
        }
    }

    /**
     * Exécuté après le contrôleur.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à exécuter après la requête pour ce filtre
    }
}