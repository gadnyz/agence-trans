<?php

namespace App\Controllers\Web;

use CodeIgniter\Controller;
use App\Models\UserModel;
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

<<<<<<< HEAD
=======
    /**
     * Constructor.
     */
>>>>>>> a08a4bcda4f048d683fa9b341e24c30a9ddf6ec6
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        \Config\Services::session();
<<<<<<< HEAD
=======
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function currentWebUser(): ?array
    {
        $user = session()->get('user');

        return is_array($user) ? $user : null;
>>>>>>> a08a4bcda4f048d683fa9b341e24c30a9ddf6ec6
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

        $user = $this->authenticatedUserFromSession();

        if ($user === null) {
            session()->destroy();
            return redirect()->to('/')->with('error', 'Session expirée, veuillez vous reconnecter.');
        }

        session()->set('user', $user);

        return $user;
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

    /**
     * Valide le token de session sans faire de loopback HTTP vers l'API locale.
     *
     * @return array<string, mixed>|null
     */
    protected function authenticatedUserFromSession(): ?array
    {
        $token = (string) session()->get('access_token');

        if ($token === '') {
            return null;
        }

        try {
            $claims = service('jwtService')->decodeAccessToken($token);
        } catch (\Throwable $e) {
            log_message('error', '[WebAuth] Invalid access token: ' . $e->getMessage());

            return null;
        }

        $user = (new UserModel())->findActiveById((int) ($claims['sub'] ?? 0));

        if ($user === null) {
            log_message('error', '[WebAuth] Session user not found for token subject.');

            return null;
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

        return $publicUser;
    }
}
