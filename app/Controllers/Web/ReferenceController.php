<?php

namespace App\Controllers\Web;

use CodeIgniter\HTTP\ResponseInterface;

class ReferenceController extends BaseWebController
{
    /**
     * Helper to authenticate user and build shared view data.
     */
    private function authAndData(string $pageTitle): array|ResponseInterface
    {
        $user = $this->requireAuthApi();

        if ($user instanceof ResponseInterface) {
            return $user;
        }

        $userRole = $this->userRole();
        $layout = match ($userRole) {
            'super_admin' => 'web/layouts/super_admin',
            'admin'       => 'web/layouts/admin',
            default       => 'web/layouts/admin',
        };

        return [
            'layout'    => $layout,
            'title'     => 'KishalaTrans — ' . $pageTitle,
            'pageTitle' => $pageTitle,
            'user'      => $user,
            'api_token' => session()->get('access_token'),
        ];
    }

    /**
     * GET /admin/bus
     */
    public function bus()
    {
        $data = $this->authAndData('Gestion des Bus');

        if ($data instanceof ResponseInterface) {
            return $data;
        }

        // Fetch all active buses (not deleted)
        $data['buses'] = db_connect()
            ->table('bus')
            ->where('deleted_at', null)
            ->orderBy('numero_plaque', 'asc')
            ->get()
            ->getResultArray();

        return view('web/admin/bus', $data);
    }

    /**
     * GET /admin/chauffeurs
     */
    public function chauffeurs()
    {
        $data = $this->authAndData('Gestion des Chauffeurs');

        if ($data instanceof ResponseInterface) {
            return $data;
        }

        // Fetch all active drivers
        $data['chauffeurs'] = db_connect()
            ->table('conducteur')
            ->where('deleted_at', null)
            ->orderBy('nom', 'asc')
            ->get()
            ->getResultArray();

        return view('web/admin/chauffeurs', $data);
    }

    /**
     * GET /admin/trajets
     */
    public function trajets()
    {
        $data = $this->authAndData('Gestion des Trajets');

        if ($data instanceof ResponseInterface) {
            return $data;
        }

        $db = db_connect();

        // Fetch all active trajets with details (lieu_depart, lieu_arrivee, horaire, currency)
        $data['trajets'] = $db->table('trajet t')
            ->select('t.*, ld.nom_lieu as lieu_depart, la.nom_lieu as lieu_arrivee, h.heure_depart, h.heure_arrivee, c.code_currency, c.symbole')
            ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
            ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
            ->join('horaire h', 'h.id_horaire = t.id_horaire')
            ->join('currency c', 'c.id_currency = t.id_currency')
            ->where('t.deleted_at', null)
            ->orderBy('t.id_trajet', 'desc')
            ->get()
            ->getResultArray();

        // Fetch options for creation/edition modals
        $data['lieux'] = $db->table('lieu')
            ->where('deleted_at', null)
            ->orderBy('nom_lieu', 'asc')
            ->get()
            ->getResultArray();

        $data['horaires'] = $db->table('horaire')
            ->where('deleted_at', null)
            ->orderBy('heure_depart', 'asc')
            ->get()
            ->getResultArray();

        $data['currencies'] = $db->table('currency')
            ->where('deleted_at', null)
            ->orderBy('code_currency', 'asc')
            ->get()
            ->getResultArray();

        return view('web/admin/trajets', $data);
    }
}
