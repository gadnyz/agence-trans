<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    // -------------------------------------------------------------------------
    // Données communes aux dashboards
    // -------------------------------------------------------------------------
    private function buildSummary(): array
    {
        return [
            'encaissements'   => 1542000,
            'paiements'       => 45,
            'reservations'    => 157,
            'sieges'          => 210,
            'clients'         => 120,
            'nouveaux_clients' => 12,
            'courses'         => 8,
            'conducteurs'     => 6,
            'bus'             => 6,
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
    // SUPER ADMIN — Dashboard complet (finances, config, stats globales)
    // Route : GET /super-admin/dashboard
    // -------------------------------------------------------------------------
    public function superAdminDashboard()
    {
        $filters = [
            'date_debut' => $this->request->getGet('date_debut') ?? date('Y-m-01'),
            'date_fin'   => $this->request->getGet('date_fin')   ?? date('Y-m-d'),
            'id_trajet'  => $this->request->getGet('id_trajet')  ?? '',
            'id_agent'   => $this->request->getGet('id_agent')   ?? '',
        ];

        $data = [
            'title'      => 'Kashala Trans — Tableau de bord Super Admin',
            'pageTitle'  => 'Tableau de bord',
            'filters'    => $filters,
            'summary'    => $this->buildSummary(),
            'pagination' => $this->buildPagination(),
            'options'    => [
                'trajets' => [], // $trajetModel->findAll()
                'agents'  => [], // $userModel->findAll()
            ],
            'details'    => [
                'reservations' => [], // résultats paginés
            ],
        ];

        return view('web/super_admin/dashboard', $data);
    }

    // -------------------------------------------------------------------------
    // ADMIN — Dashboard métier (sans config système ni paramètres critiques)
    // Route : GET /admin/dashboard
    // -------------------------------------------------------------------------
    public function adminDashboard()
    {
        $filters = [
            'date_debut' => $this->request->getGet('date_debut') ?? date('Y-m-01'),
            'date_fin'   => $this->request->getGet('date_fin')   ?? date('Y-m-d'),
            'id_trajet'  => $this->request->getGet('id_trajet')  ?? '',
        ];

        $data = [
            'title'      => 'Kashala Trans — Tableau de bord Admin',
            'pageTitle'  => 'Tableau de bord',
            'filters'    => $filters,
            'summary'    => $this->buildSummary(),
            'pagination' => $this->buildPagination(),
            'options'    => [
                'trajets' => [],
            ],
            'details'    => [
                'reservations' => [],
            ],
        ];

        return view('web/admin/dashboard', $data);
    }

    // -------------------------------------------------------------------------
    // Méthode legacy — redirige selon le rôle réel de l'utilisateur
    // (fallback pour anciens liens /super-admin/dashboard sans filtre de rôle)
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