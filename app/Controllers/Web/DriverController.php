<?php

namespace App\Controllers\Web;

class DriverController extends BaseWebController
{
    // -------------------------------------------------------------------------
    // CHAUFFEUR — Planning du jour
    // Route : GET /driver/planning
    // -------------------------------------------------------------------------
    public function planning()
    {
        $user = $this->requireAuthApi();

        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $userRole = $this->userRole();
        $layout = match($userRole) {
            'super_admin' => 'web/layouts/super_admin',
            'admin'       => 'web/layouts/admin',
            'driver'      => 'web/layouts/driver',
            default       => 'web/layouts/driver',
        };

        $db = \Config\Database::connect();
        $conducteur = null;

        // 1. Try matching by phone
        if (!empty($user['telephone'])) {
            $conducteur = $db->table('conducteur')
                ->where('telephone', $user['telephone'])
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();
        }

        // 2. Try matching by prenom and nom/postnom
        if (!$conducteur && !empty($user['prenom'])) {
            $qb = $db->table('conducteur')
                ->where('prenom', $user['prenom'])
                ->where('deleted_at', null);

            if (!empty($user['nom'])) {
                $qb->groupStart()
                   ->where('nom', $user['nom'])
                   ->orWhere('postnom', $user['nom'])
                   ->groupEnd();
            }
            $conducteur = $qb->get()->getRowArray();
        }

        // 3. Fallback for testing/demos: pick first active driver
        if (!$conducteur) {
            $conducteur = $db->table('conducteur')
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();
        }

        $programmes = [];
        $driverStats = null;

        if ($conducteur) {
            // Fetch today's planning
            $programmes = $db->table('programme p')
                ->select([
                    'p.*',
                    'b.numero_plaque',
                    'b.nombre_places',
                    'td.nom_lieu AS lieu_depart',
                    'ta.nom_lieu AS lieu_arrivee',
                    'h.heure_depart',
                    'h.heure_arrivee',
                    '(b.nombre_places - p.places_disponibles) AS nb_passagers'
                ])
                ->join('bus b', 'b.id_bus = p.id_bus')
                ->join('conducteur c', 'c.id_conducteur = p.id_conducteur')
                ->join('trajet t', 't.id_trajet = p.id_trajet')
                ->join('lieu td', 'td.id_lieu = t.id_lieu_depart')
                ->join('lieu ta', 'ta.id_lieu = t.id_lieu_arrivee')
                ->join('horaire h', 'h.id_horaire = t.id_horaire')
                ->where('p.id_conducteur', $conducteur['id_conducteur'])
                ->where('p.date_programme', date('Y-m-d'))
                ->where('p.deleted_at', null)
                ->where('b.deleted_at', null)
                ->where('c.deleted_at', null)
                ->where('t.deleted_at', null)
                ->orderBy('h.heure_depart', 'asc')
                ->get()
                ->getResultArray();

            // Fetch cumulative stats for driver
            $driverStats = $db->table('v_analyse_performance_conducteurs')
                ->where('id_conducteur', $conducteur['id_conducteur'])
                ->get()
                ->getRowArray();
        }

        $data = [
            'layout'       => $layout,
            'title'        => 'Kashala Trans — Mon Planning',
            'pageTitle'    => 'Mon Planning',
            'user'         => $user,
            'conducteur'   => $conducteur,
            'driverStats'  => $driverStats,
            'api_token'    => session()->get('access_token'),
            'programmes'   => $programmes,
        ];

        return view('web/driver/planning', $data);
    }
}
