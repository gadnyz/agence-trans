<?php

namespace App\Controllers\Web;

class AuthController extends BaseWebController
{
    // public function index()
    // {
    //     // Si deja connecte, ouvrir directement les reservations.
    //     if (session()->get('access_token')) {
    //         return redirect()->to('/reservations');
    //     }

    //     return view('web/auth/connexion');
    // }

    public function index()
    {
        // 1. Vérifier si l'utilisateur est connecté
        $user = session()->get('user');

        if (!$user) {
            return view('web/auth/connexion');
        }

        // 2. Rediriger selon le rôle (Si déjà connecté, on l'envoie sur sa page d'accueil)
        $role = $user['role']['code'] ?? ''; // Accès au code selon ta structure JWT

        switch ($role) {
            case 'super_admin':
                return redirect()->to('/super-admin/dashboard');
            case 'admin':
                return redirect()->to('/admin/dashboard');
            case 'recept':
                return redirect()->to('/recept/reservations');
            case 'driver':
                return redirect()->to('/driver/planning');
            default:
                return redirect()->to('/logout')->with('error', 'Rôle inconnu.');
        }
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Appel de l'API interne
        $response = $this->api->post('auth/login', [
            'username' => $username,
            'password' => $password
        ]);

        if ($response && isset($response['success']) && $response['success'] === true) {
            // Connexion réussie, stocker les infos en session
            session()->set('access_token', $response['data']['token']['access_token']);
            session()->set('refresh_token', $response['data']['token']['refresh_token']);
            session()->set('user', $response['data']['user']);

            // return redirect()->to('/reservations');
        } else {
            // Echec de connexion
            $errorMsg = (is_array($response) && isset($response['message'])) ? $response['message'] : 'Identifiants invalides ou erreur de communication avec le serveur (Timeout).';
            return redirect()->back()->with('error', $errorMsg);
        }
    }

    public function logout()
    {
        $refreshToken = session()->get('refresh_token');
        if ($refreshToken) {
            // Appel de l'API interne pour révoquer le token
            $this->api->post('auth/logout', [
                'refresh_token' => $refreshToken
            ]);
        }

        session()->destroy();
        return redirect()->to('/');
    }

    public function refresh()
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

        $response = $this->api->post('auth/refresh', [
            'refresh_token' => $refreshToken,
        ]);

        if (($response['success'] ?? false) !== true) {
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

        session()->set('access_token', $response['data']['token']['access_token']);
        session()->set('refresh_token', $response['data']['token']['refresh_token']);
        session()->set('user', $response['data']['user']);

        return $this->response
            ->setJSON([
                'success' => true,
                'message' => 'Session renouvelee.',
                'data' => [
                    'access_token' => $response['data']['token']['access_token'],
                ],
                'errors' => null,
            ]);
    }
}
