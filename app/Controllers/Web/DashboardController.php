<?php

namespace App\Controllers\Web;

class DashboardController extends BaseWebController
{
    /**
     * Build real summary metrics based on selected filters.
     */
    private function buildSummary(array $filters): array
    {
        $db = \Config\Database::connect();
        
        // 1. Reservations & Clients & Seats
        $qb = $db->table('reservation r')
            ->select([
                'COUNT(DISTINCT r.id_reservation) AS reservations',
                'COUNT(DISTINCT r.id_client) AS clients',
                'COALESCE(SUM(r.nombre_places), 0) AS sieges',
            ])
            ->join('programme p', 'p.id_programme = r.id_programme')
            ->where('r.deleted_at', null)
            ->where('DATE(r.date_reservation) >=', $filters['date_debut'])
            ->where('DATE(r.date_reservation) <=', $filters['date_fin']);
            
        if (!empty($filters['id_trajet'])) {
            $qb->where('p.id_trajet', $filters['id_trajet']);
        }
        if (!empty($filters['id_agent'])) {
            $qb->where('r.created_by', $filters['id_agent']);
        }
        
        $resStats = $qb->get()->getRowArray() ?: [];

        // 2. Payments & Encaissements (Valides)
        $qbPay = $db->table('paiement pa')
            ->select([
                'COUNT(pa.id_paiement) AS paiements',
                'COALESCE(SUM(pa.montant_paye), 0) AS encaissements'
            ])
            ->join('reservation r', 'r.id_reservation = pa.id_reservation')
            ->join('programme p', 'p.id_programme = r.id_programme')
            ->where('pa.deleted_at', null)
            ->where('pa.statut_paiement', 'Valide')
            ->where('DATE(pa.date_paiement) >=', $filters['date_debut'])
            ->where('DATE(pa.date_paiement) <=', $filters['date_fin']);
            
        if (!empty($filters['id_trajet'])) {
            $qbPay->where('p.id_trajet', $filters['id_trajet']);
        }
        if (!empty($filters['id_agent'])) {
            $qbPay->where('r.created_by', $filters['id_agent']);
        }
        
        $payStats = $qbPay->get()->getRowArray() ?: [];

        // 3. New clients
        $newClients = $db->table('client')
            ->where('deleted_at', null)
            ->where('DATE(created_at) >=', $filters['date_debut'])
            ->where('DATE(created_at) <=', $filters['date_fin'])
            ->countAllResults();

        // 4. Courses (Programmes run)
        $qbProg = $db->table('programme p')
            ->where('p.deleted_at', null)
            ->where('p.date_programme >=', $filters['date_debut'])
            ->where('p.date_programme <=', $filters['date_fin']);
        if (!empty($filters['id_trajet'])) {
            $qbProg->where('p.id_trajet', $filters['id_trajet']);
        }
        $courses = $qbProg->countAllResults();

        // 5. Active buses and drivers in that period
        $qbBus = $db->table('programme p')
            ->select('COUNT(DISTINCT p.id_bus) as total')
            ->where('p.deleted_at', null)
            ->where('p.date_programme >=', $filters['date_debut'])
            ->where('p.date_programme <=', $filters['date_fin']);
        if (!empty($filters['id_trajet'])) {
            $qbBus->where('p.id_trajet', $filters['id_trajet']);
        }
        $busCount = $qbBus->get()->getRowArray()['total'] ?? 0;

        $qbCond = $db->table('programme p')
            ->select('COUNT(DISTINCT p.id_conducteur) as total')
            ->where('p.deleted_at', null)
            ->where('p.date_programme >=', $filters['date_debut'])
            ->where('p.date_programme <=', $filters['date_fin']);
        if (!empty($filters['id_trajet'])) {
            $qbCond->where('p.id_trajet', $filters['id_trajet']);
        }
        $condCount = $qbCond->get()->getRowArray()['total'] ?? 0;

        return [
            'encaissements'    => (float)($payStats['encaissements'] ?? 0),
            'paiements'        => (int)($payStats['paiements'] ?? 0),
            'reservations'     => (int)($resStats['reservations'] ?? 0),
            'sieges'           => (int)($resStats['sieges'] ?? 0),
            'clients'          => (int)($resStats['clients'] ?? 0),
            'nouveaux_clients' => $newClients,
            'courses'          => $courses,
            'conducteurs'      => $condCount,
            'bus'              => $busCount,
        ];
    }

    private function buildPagination(): array
    {
        return [
            'page'        => 1,
            'per_page'    => 10,
            'total'       => 0,
            'total_pages' => 1,
            'from'        => 0,
            'to'          => 0,
        ];
    }

    // -------------------------------------------------------------------------
    // SUPER ADMIN — Dashboard complet
    // Route : GET /super-admin/dashboard
    // -------------------------------------------------------------------------
    public function superAdminDashboard()
    {
        $user = $this->requireAuthApi();

        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $db = \Config\Database::connect();

        $filters = [
            'date_debut' => $this->request->getGet('date_debut') ?? date('Y-m-01'),
            'date_fin'   => $this->request->getGet('date_fin')   ?? date('Y-m-d'),
            'id_trajet'  => $this->request->getGet('id_trajet')  ?? '',
            'id_agent'   => $this->request->getGet('id_agent')   ?? '',
        ];

        // Fetch recent reservations with filters applied if provided (or just latest overall)
        $qbRes = $db->table('reservation r')
            ->select('r.*, cl.nom as client, cl.telephone as client_telephone, CONCAT(ld.nom_lieu, " - ", la.nom_lieu) as trajet, h.heure_depart, st.libelle as statut, c.symbole')
            ->join('client cl', 'cl.id_client = r.id_client')
            ->join('programme p', 'p.id_programme = r.id_programme')
            ->join('trajet t', 't.id_trajet = p.id_trajet')
            ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
            ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
            ->join('horaire h', 'h.id_horaire = t.id_horaire')
            ->join('currency c', 'c.id_currency = r.id_currency')
            ->join('statut_reservation st', 'st.id_statut_reservation = r.id_statut_reservation')
            ->where('r.deleted_at', null)
            ->orderBy('r.created_at', 'desc')
            ->limit(10);

        if (!empty($filters['id_trajet'])) {
            $qbRes->where('p.id_trajet', $filters['id_trajet']);
        }
        if (!empty($filters['id_agent'])) {
            $qbRes->where('r.created_by', $filters['id_agent']);
        }

        $reservations = $qbRes->get()->getResultArray();

        $data = [
            'layout'     => 'web/layouts/super_admin',
            'title'      => 'KishalaTrans — Tableau de bord Super Admin',
            'pageTitle'  => 'Tableau de bord',
            'user'       => $user,
            'filters'    => $filters,
            'summary'    => $this->buildSummary($filters),
            'pagination' => $this->buildPagination(),
            'options'    => [
                'trajets' => $db->table('trajet t')
                    ->select('t.id_trajet, ld.nom_lieu AS lieu_depart, la.nom_lieu AS lieu_arrivee, h.heure_depart, h.heure_arrivee')
                    ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
                    ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
                    ->join('horaire h', 'h.id_horaire = t.id_horaire')
                    ->where('t.deleted_at', null)
                    ->orderBy('ld.nom_lieu')
                    ->get()
                    ->getResultArray(),
                'agents'  => $db->table('utilisateur')
                    ->select('id_utilisateur, username, nom, postnom, prenom')
                    ->where('deleted_at', null)
                    ->orderBy('username', 'asc')
                    ->get()
                    ->getResultArray(),
            ],
            'details'    => [
                'reservations' => $reservations,
            ],
        ];

        return view('web/super_admin/dashboard', $data);
    }

    // -------------------------------------------------------------------------
    // ADMIN — Dashboard métier
    // Route : GET /admin/dashboard
    // -------------------------------------------------------------------------
    public function adminDashboard()
    {
        $user = $this->requireAuthApi();

        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $db = \Config\Database::connect();

        $filters = [
            'date_debut' => $this->request->getGet('date_debut') ?? date('Y-m-01'),
            'date_fin'   => $this->request->getGet('date_fin')   ?? date('Y-m-d'),
            'id_trajet'  => $this->request->getGet('id_trajet')  ?? '',
            'id_agent'   => '',
        ];

        $qbRes = $db->table('reservation r')
            ->select('r.*, cl.nom as client, cl.telephone as client_telephone, CONCAT(ld.nom_lieu, " - ", la.nom_lieu) as trajet, h.heure_depart, st.libelle as statut, c.symbole')
            ->join('client cl', 'cl.id_client = r.id_client')
            ->join('programme p', 'p.id_programme = r.id_programme')
            ->join('trajet t', 't.id_trajet = p.id_trajet')
            ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
            ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
            ->join('horaire h', 'h.id_horaire = t.id_horaire')
            ->join('currency c', 'c.id_currency = r.id_currency')
            ->join('statut_reservation st', 'st.id_statut_reservation = r.id_statut_reservation')
            ->where('r.deleted_at', null)
            ->orderBy('r.created_at', 'desc')
            ->limit(10);

        if (!empty($filters['id_trajet'])) {
            $qbRes->where('p.id_trajet', $filters['id_trajet']);
        }

        $reservations = $qbRes->get()->getResultArray();

        $data = [
            'layout'     => 'web/layouts/admin',
            'title'      => 'KishalaTrans — Tableau de bord Admin',
            'pageTitle'  => 'Tableau de bord',
            'user'       => $user,
            'filters'    => $filters,
            'summary'    => $this->buildSummary($filters),
            'pagination' => $this->buildPagination(),
            'options'    => [
                'trajets' => $db->table('trajet t')
                    ->select('t.id_trajet, ld.nom_lieu AS lieu_depart, la.nom_lieu AS lieu_arrivee, h.heure_depart, h.heure_arrivee')
                    ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
                    ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
                    ->join('horaire h', 'h.id_horaire = t.id_horaire')
                    ->where('t.deleted_at', null)
                    ->orderBy('ld.nom_lieu')
                    ->get()
                    ->getResultArray(),
            ],
            'details'    => [
                'reservations' => $reservations,
            ],
        ];

        return view('web/admin/dashboard', $data);
    }

    // -------------------------------------------------------------------------
    // Index legacy
    // -------------------------------------------------------------------------
    public function index()
    {
        $user = session()->get('user');
        $role = $user['role']['code'] ?? '';

        return match ($role) {
            'super_admin' => redirect()->to('/super-admin/dashboard'),
            'admin'       => redirect()->to('/admin/dashboard'),
            'recept'      => redirect()->to('/recept/reservations'),
            'driver'      => redirect()->to('/driver/planning'),
            default       => redirect()->to('/logout')->with('error', 'Rôle inconnu.'),
        };
    }
}