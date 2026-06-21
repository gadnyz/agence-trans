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
        // AJOUTE CETTE LIGNE :
        'api_token' => session()->get('access_token'), 
    ];

    return view('web/recept/reservations', $data);
}
}
