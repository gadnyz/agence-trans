<?php

namespace App\Controllers\Web;

class ReceptController extends BaseWebController
{
    // -------------------------------------------------------------------------
    // RÉCEPTIONNISTE — Page principale : liste des réservations + formulaire
    // Route : GET /recept/reservations
    // -------------------------------------------------------------------------
   public function index()
{
    $user = $this->requireAuthApi();

    if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
        return $user;
    }

    $userRole = $this->userRole();
    $layout = match($userRole) {
        'super_admin' => 'web/layouts/super_admin',
        'admin'       => 'web/layouts/admin',
        'recept'      => 'web/layouts/recept',
        default       => 'web/layouts/recept',
    };

    $data = [
        'layout'    => $layout,
        'title'     => 'Kashala Trans — Guichet',
        'pageTitle' => 'Réservations',
        'user'      => $user,
        'api_token' => session()->get('access_token'), 
        'modes_paiement' => db_connect()->table('mode_paiement')->where('deleted_at', null)->get()->getResultArray(),
    ];

    return view('web/recept/reservations', $data);
}
}
