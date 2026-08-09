<?php

namespace App\Controllers\Web;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseWebController
{
    public function index()
    {
        if (session()->get('access_token')) {
            return redirect()->to($this->roleHomePath());
        }

        return view('web/auth/connexion');
    }

    public function login()
    {
        try {
            $username = trim((string) $this->request->getPost('username'));
            $password = (string) $this->request->getPost('password');

            if ($username === '' || $password === '') {
                return redirect()->to('/')->with('error', 'Identifiants requis.');
            }

            $user = (new UserModel())->findActiveByUsername($username);

            if ($user === null || ! password_verify($password, (string) $user['mot_de_passe'])) {
                return redirect()->to('/')->with('error', 'Identifiants invalides.');
            }

            (new UserModel())->touchLastConnected((int) $user['id_utilisateur']);

            $jwt = service('jwtService');
            $tokenPair = $jwt->createTokenPair(
                $user,
                $this->request->getUserAgent()->getAgentString(),
                $this->request->getIPAddress()
            );
            $publicUser = $jwt->publicUser($user);

            session()->set('access_token', $tokenPair['access_token']);
            session()->set('refresh_token', $tokenPair['refresh_token']);
            session()->set('user', $publicUser);

            $role = $publicUser['role']['code'] ?? '';

            return redirect()->to($this->roleHomePath($role));
        } catch (\Throwable $e) {
            log_message('error', '[WebAuth] Login failed: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return redirect()->to('/')->with(
                'error',
                ENVIRONMENT === 'development'
                    ? 'Erreur connexion: ' . $e->getMessage()
                    : 'Erreur de connexion. Veuillez reessayer.'
            );
        }
    }

    public function logout()
    {
        $jwt = service('jwtService');
        $refreshToken = session()->get('refresh_token');

        if (is_string($refreshToken) && $refreshToken !== '') {
            $jwt->revokeRefreshToken($refreshToken);
        }

        $accessToken = (string) session()->get('access_token');
        if ($accessToken !== '') {
            try {
                $claims = $jwt->decodeAccessToken($accessToken);
                $jwt->revokeAccessTokenClaims($claims);
            } catch (\Throwable $e) {
                log_message('error', '[WebAuth] Logout token revoke failed: ' . $e->getMessage());
            }
        }

        session()->destroy();

        return redirect()->to('/');
    }

    public function refresh(): ResponseInterface
    {
        $refreshToken = session()->get('refresh_token');

        if (! $refreshToken) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Session expiree. Veuillez vous reconnecter.',
                    'data' => null,
                    'errors' => null,
                ]);
        }

        $response = service('jwtService')->refresh(
            (string) $refreshToken,
            $this->request->getUserAgent()->getAgentString(),
            $this->request->getIPAddress()
        );

        if ($response === null) {
            session()->destroy();

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Session expiree. Veuillez vous reconnecter.',
                    'data' => null,
                    'errors' => null,
                ]);
        }

        session()->set('access_token', $response['token']['access_token']);
        session()->set('refresh_token', $response['token']['refresh_token']);
        session()->set('user', $response['user']);

        return $this->response
            ->setJSON([
                'success' => true,
                'message' => 'Session renouvelee.',
                'data' => [
                    'access_token' => $response['token']['access_token'],
                ],
                'errors' => null,
            ]);
    }
}