<?php

namespace App\Controllers\Web;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseWebController extends Controller
{
    /**
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    protected $helpers = ['url', 'form'];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        \Config\Services::session();
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function currentWebUser(): ?array
    {
        $user = session()->get('user');

        return is_array($user) ? $user : null;
    }

    // -------------------------------------------------------------------------
    // Helpers communs — disponibles dans tous les controllers Web
    // -------------------------------------------------------------------------

    /**
     * Retourne l'utilisateur connecté depuis la session.
     */
    protected function sessionUser(): array
    {
        return session()->get('user') ?? [];
    }

    /**
     * Retourne le code du rôle de l'utilisateur connecté.
     */
    protected function userRole(): string
    {
        return $this->sessionUser()['role']['code'] ?? '';
    }

    /**
     * Vérifie que l'utilisateur a un token valide (API auth/me).
     * Retourne le tableau de données utilisateur ou une Response de redirection.
     */
    protected function requireAuthApi(): array|ResponseInterface
    {
        if (! session()->get('access_token')) {
            return redirect()->to('/');
        }

        $meResponse = $this->api->get('auth/me');

        if (! $meResponse || ($meResponse['success'] ?? false) === false) {
            session()->destroy();
            return redirect()->to('/')->with('error', 'Session expirée, veuillez vous reconnecter.');
        }

        return $meResponse['data'];
    }

    /**
     * Vérifie qu'il y a un token de session (sans appel API) — pour les pages print.
     */
    protected function requireSession(): ?ResponseInterface
    {
        if (! session()->get('access_token')) {
            return redirect()->to('/');
        }
        return null;
    }

    /**
     * Table de la page d'accueil par rôle.
     */
    protected function roleHomePath(string $role = ''): string
    {
        $role = $role ?: $this->userRole();

        return match ($role) {
            'super_admin' => '/super-admin/dashboard',
            'admin'       => '/admin/rapports',
            'recept'      => '/recept/reservations',
            'driver'      => '/driver/planning',
            default       => '/',
        };
    }
}
