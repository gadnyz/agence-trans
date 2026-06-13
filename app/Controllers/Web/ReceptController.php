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
        $data = [
            'title'     => 'Kashala Trans — Guichet',
            'pageTitle' => 'Réservations',
        ];

        return view('web/recept/reservations', $data);
    }
}
