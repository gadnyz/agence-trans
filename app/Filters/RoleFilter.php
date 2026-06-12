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
        // Récupère le rôle actuellement stocké dans la session lors de la connexion
        $userRole = session()->get('role');

        // Si l'utilisateur n'a pas de rôle ou si aucun argument n'est passé au filtre
        if (!$userRole) {
            return redirect()->to('/')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }

        // Si aucun rôle spécifique n'est requis dans la définition du filtre
        if (empty($arguments)) {
            return;
        }

        // Vérifie si le rôle de l'utilisateur fait partie des rôles autorisés ($arguments)
        if (!in_array($userRole, $arguments, true)) {
            // Optionnel : Gérer une redirection différente selon s'il s'agit d'une API ou d'une vue Web
            if (strpos($request->getPath(), 'api/') === 0) {
                return service('response')->setStatusCode(403, 'Accès refusé (Rôle insuffisant).');
            }

            // Redirection par défaut vers la page d'accueil ou tableau de bord approprié
            return redirect()->to('/')->with('error', 'Vous n\'avez pas les privilèges requis pour accéder à cette page.');
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