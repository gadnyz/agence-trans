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

        return view('web/super_admin/parametres', [
            'title'     => 'Paramètres',
            'pageTitle' => 'Paramètres',
            'user'      => $meResponse,
        ]);
    }
}
