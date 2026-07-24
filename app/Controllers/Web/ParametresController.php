<?php

namespace App\Controllers\Web;

use CodeIgniter\HTTP\ResponseInterface;

class ParametresController extends BaseWebController
{
    public function index()
    {
        $meResponse = $this->requireAuthApi();

        if ($meResponse instanceof ResponseInterface) {
            return $meResponse;
        }

        $db = db_connect();

        $utilisateurs = $db->table('utilisateur u')
            ->select('u.*, r.libelle as role_libelle')
            ->join('role r', 'r.id_role = u.id_role')
            ->where('u.deleted_at', null)
            ->orderBy('u.nom', 'asc')
            ->get()
            ->getResultArray();

        $roles = $db->table('role')
            ->where('deleted_at', null)
            ->orderBy('libelle', 'asc')
            ->get()
            ->getResultArray();

        return view('web/super_admin/parametres', [
            'title'        => 'Paramètres',
            'pageTitle'    => 'Paramètres',
            'user'         => $meResponse,
            'api_token'    => session()->get('access_token'),
            'utilisateurs' => $utilisateurs,
            'roles'        => $roles,
        ]);
    }
}
