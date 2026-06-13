<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        // 1. Récupérer les filtres depuis l'URL (GET) ou mettre des valeurs par défaut
        $filters = [
            'date_debut' => $this->request->getGet('date_debut') ?? date('Y-m-01'),
            'date_fin'   => $this->request->getGet('date_fin') ?? date('Y-m-d'),
            'id_trajet'  => $this->request->getGet('id_trajet') ?? '',
            'id_agent'   => $this->request->getGet('id_agent') ?? ''
        ];

        // 2. Simuler ou requêter les données de la base de données
        // (Ici tu appelleras tes Models pour avoir les vrais chiffres)
        $summary = [
            'encaissements' => 1542000, 
            'paiements' => 45, 
            'reservations' => 157, 
            'sieges' => 210,
            'clients' => 120, 
            'nouveaux_clients' => 12, 
            'courses' => 8, 
            'conducteurs' => 6, 
            'bus' => 6
        ];

        $pagination = [
            'page' => 1, 
            'per_page' => 10, 
            'total' => 0, // Mettre le vrai total de lignes
            'total_pages' => 1, 
            'from' => 0, 
            'to' => 0
        ];

        $data = [
            'title'      => 'Kashala Trans - Tableau de bord',
            'pageTitle'  => 'Tableau de bord',
            'filters'    => $filters,
            'summary'    => $summary,
            'pagination' => $pagination,
            'options'    => [
                'trajets' => [], // Passer `$trajetModel->findAll()`
                'agents'  => []  // Passer `$userModel->findAll()`
            ],
            'details'    => [
                'reservations' => [] // Passer les résultats paginés ici
            ]
        ];

        return view('web/super_admin/dashboard', $data);
    }
}