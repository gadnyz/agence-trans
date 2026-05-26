<?php

namespace App\Controllers\Web;

use App\Models\UserModel;

class AuthController extends BaseWebController
{
    public function index()
    {
        if (session()->get('access_token')) {
            return redirect()->to('/reservations');
        }

        return view('web/pages/connexion');
    }

    public function login()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if ($username === '' || $password === '') {
            return redirect()->back()->with('error', 'Identifiants requis.');
        }

        $userModel = new UserModel();
        $user = $userModel->findActiveByUsername($username);

        if ($user === null || ! password_verify($password, (string) $user['mot_de_passe'])) {
            return redirect()->back()->with('error', 'Identifiants invalides.');
        }

        $userModel->touchLastConnected((int) $user['id_utilisateur']);

        $jwt = service('jwtService');
        $token = $jwt->createTokenPair(
            $user,
            $this->request->getUserAgent()->getAgentString(),
            $this->request->getIPAddress()
        );

        session()->set('access_token', $token['access_token']);
        session()->set('refresh_token', $token['refresh_token']);
        session()->set('user', $jwt->publicUser($user));

        return redirect()->to('/reservations');
    }

    public function logout()
    {
        $refreshToken = session()->get('refresh_token');

        if ($refreshToken) {
            service('jwtService')->revokeRefreshToken((string) $refreshToken);
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
