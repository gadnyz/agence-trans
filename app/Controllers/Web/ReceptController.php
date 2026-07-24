<?php

namespace App\Controllers\Web;

class ReceptController extends BaseWebController
{
    // -------------------------------------------------------------------------
    // Helper privé : authentification + données communes à toutes les pages
    // -------------------------------------------------------------------------
    private function authAndData(string $pageTitle, array $extra = []): array|object
    {
        $user = $this->requireAuthApi();

        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $userRole = $this->userRole();
        $layout   = match ($userRole) {
            'super_admin' => 'web/layouts/super_admin',
            'admin'       => 'web/layouts/admin',
            'recept'      => 'web/layouts/recept',
            default       => 'web/layouts/recept',
        };

        return array_merge([
            'layout'          => $layout,
            'title'           => 'KishalaTrans — Guichet',
            'pageTitle'       => $pageTitle,
            'user'            => $user,
            'api_token'       => session()->get('access_token'),
            'modes_paiement'  => db_connect()
                ->table('mode_paiement')
                ->where('deleted_at', null)
                ->get()
                ->getResultArray(),
        ], $extra);
    }

    // -------------------------------------------------------------------------
    // PAGE 1 : Tableau de bord (Accueil Guichet)
    // Route : GET /recept/dashboard
    // -------------------------------------------------------------------------
    public function index()
    {
        $data = $this->authAndData('Tableau de bord');

        if ($data instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $data;
        }

        return view('web/recept/dashboard', $data);
    }

    // -------------------------------------------------------------------------
    // PAGE 2 : Gestion des Réservations
    // Route : GET /recept/reservations
    // -------------------------------------------------------------------------
    public function reservations()
    {
        $data = $this->authAndData('Réservations');

        if ($data instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $data;
        }

        return view('web/recept/reservations', $data);
    }

    // -------------------------------------------------------------------------
    // PAGE 3 : Paiements
    // Route : GET /recept/paiements
    // -------------------------------------------------------------------------
    public function paiements()
    {
        $data = $this->authAndData('Paiements');

        if ($data instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $data;
        }

        return view('web/recept/paiements', $data);
    }

    // -------------------------------------------------------------------------
    // PAGE 4 : Programmes / Voyages
    // Route : GET /recept/programmes
    // -------------------------------------------------------------------------
    public function programmes()
    {
        $data = $this->authAndData('Programmes de voyage');

        if ($data instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $data;
        }

        return view('web/recept/programmes', $data);
    }

    // -------------------------------------------------------------------------
    // PAGE 5 : Consultation Flotte & Réseau
    // Route : GET /recept/flotte
    // -------------------------------------------------------------------------
    public function flotte()
    {
        $data = $this->authAndData('Flotte & Réseau');

        if ($data instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $data;
        }

        return view('web/recept/flotte', $data);
    }
}
