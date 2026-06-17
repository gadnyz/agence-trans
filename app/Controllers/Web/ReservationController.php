<?php

namespace App\Controllers\Web;

class ReservationController extends BaseWebController
{
    public function index()
    {
        $user = $this->requireAuthApi();

        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        // Choisir la vue selon le rôle de l'utilisateur
        $userRole = $this->userRole();

        $viewMap = [
            'super_admin' => 'web/super_admin/reservation',
            'admin'       => 'web/super_admin/reservation',
            'recept'      => 'web/recept/reservations',
        ];

        $viewName = $viewMap[$userRole] ?? 'web/super_admin/reservation';

        $layout = match($userRole) {
            'super_admin' => 'web/layouts/super_admin',
            'admin'       => 'web/layouts/admin',
            'recept'      => 'web/layouts/recept',
            default       => 'web/layouts/super_admin',
        };

        // RÉCUPÉRATION DES RÉSERVATIONS ICI (Triées par date de création descendante)
        $reservations = $this->reservationBuilder()
            ->orderBy('r.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view($viewName, [
            'layout'         => $layout,
            'title'          => 'Réservations',
            'user'           => $user,
            'api_token'      => session()->get('access_token'),
            'modes_paiement' => $this->modesPaiement(),
            'today'          => date('Y-m-d'),
            'reservations'   => $reservations, // AJOUT DE LA VARIABLE POUR LA VUE
        ]);
    }


    public function ticket(int $idReservation)
    {
        $guardResponse = $this->printSessionGuard();

        if ($guardResponse !== null) {
            return $guardResponse;
        }

        $reservation = $this->reservationDetail($idReservation);

        if ($reservation === null) {
            return redirect()->to('/super-admin/reservation')->with('error', 'Reservation introuvable.');
        }

        return view('web/print/ticket', [
            'title' => 'Ticket ' . ($reservation['reference_reservation'] ?? ''),
            'reservation' => $reservation,
            'configuration' => $this->configuration(),
        ]);
    }

    public function manifeste(int $idProgramme)
    {
        $guardResponse = $this->printSessionGuard();

        if ($guardResponse !== null) {
            return $guardResponse;
        }

        $programme = $this->programmeDetail($idProgramme);

        if ($programme === null) {
            return redirect()->to('/planification')->with('error', 'Programme introuvable.');
        }

        return view('web/print/manifeste', [
            'title' => 'Liste passagers',
            'programme' => $programme,
            'reservations' => $this->programmeReservations($idProgramme),
            'configuration' => $this->configuration(),
        ]);
    }


    private function printSessionGuard()
    {
        if (! session()->get('access_token')) {
            return redirect()->to('/');
        }

        return null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function modesPaiement(): array
    {
        return db_connect()
            ->table('mode_paiement')
            ->where('deleted_at', null)
            ->orderBy('libelle', 'asc')
            ->get()
            ->getResultArray();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function reservationDetail(int $idReservation): ?array
    {
        $row = $this->reservationBuilder()
            ->where('r.id_reservation', $idReservation)
            ->get()
            ->getRowArray();

        return is_array($row) ? $row : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function programmeDetail(int $idProgramme): ?array
    {
        $row = db_connect()
            ->table('programme p')
            ->select([
                'p.*',
                'b.numero_plaque',
                'b.marque',
                'b.modele',
                'b.nombre_places',
                'c.nom AS conducteur_nom',
                'c.postnom AS conducteur_postnom',
                'c.prenom AS conducteur_prenom',
                'c.telephone AS conducteur_telephone',
                'ld.nom_lieu AS lieu_depart',
                'la.nom_lieu AS lieu_arrivee',
                'h.heure_depart',
                'h.heure_arrivee',
            ])
            ->join('bus b', 'b.id_bus = p.id_bus')
            ->join('conducteur c', 'c.id_conducteur = p.id_conducteur')
            ->join('trajet t', 't.id_trajet = p.id_trajet')
            ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
            ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
            ->join('horaire h', 'h.id_horaire = t.id_horaire')
            ->where('p.id_programme', $idProgramme)
            ->where('p.deleted_at', null)
            ->get()
            ->getRowArray();

        return is_array($row) ? $row : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function programmeReservations(int $idProgramme): array
    {
        return $this->reservationBuilder()
            ->where('r.id_programme', $idProgramme)
            ->orderBy('CASE WHEN ad.id_arret IS NULL THEN 1 ELSE 0 END', '', false)
            ->orderBy('CASE WHEN ad.ordre_arret IS NULL THEN 1 ELSE 0 END', '', false)
            ->orderBy('ad.ordre_arret', 'asc')
            ->orderBy('r.reference_reservation', 'asc')
            ->get()
            ->getResultArray();
    }

    private function reservationBuilder()
    {
        return db_connect()
            ->table('reservation r')
            ->select([
                'r.*',
                'cl.nom AS client_nom',
                'cl.telephone AS client_telephone',
                'cl.email AS client_email',
                'p.date_programme',
                'b.numero_plaque',
                'b.marque',
                'b.modele',
                'c.nom AS conducteur_nom',
                'c.postnom AS conducteur_postnom',
                'c.prenom AS conducteur_prenom',
                'c.telephone AS conducteur_telephone',
                'ld.nom_lieu AS lieu_depart',
                'la.nom_lieu AS lieu_arrivee',
                'lr.nom_lieu AS lieu_reservation',
                'ad.id_arret AS id_arret_destination',
                'ad.ordre_arret AS ordre_arret_destination',
                'h.heure_depart',
                'h.heure_arrivee',
                'cur.code_currency',
                'cur.symbole',
                'mp.libelle AS mode_paiement',
                'pa.reference_paiement',
                'pa.statut_paiement',
                'pa.date_paiement',
                'statut_reservation.libelle AS statut_reservation',
            ])
            ->join('client cl', 'cl.id_client = r.id_client')
            ->join('programme p', 'p.id_programme = r.id_programme')
            ->join('bus b', 'b.id_bus = p.id_bus')
            ->join('conducteur c', 'c.id_conducteur = p.id_conducteur')
            ->join('trajet t', 't.id_trajet = p.id_trajet')
            ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
            ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
            ->join('lieu lr', 'lr.id_lieu = r.id_lieu_reservation', 'left')
            ->join('arret ad', 'ad.id_trajet = t.id_trajet AND ad.id_lieu = r.id_lieu_reservation AND ad.deleted_at IS NULL', 'left')
            ->join('horaire h', 'h.id_horaire = t.id_horaire')
            ->join('currency cur', 'cur.id_currency = r.id_currency')
            ->join('paiement pa', 'pa.id_reservation = r.id_reservation AND pa.deleted_at IS NULL', 'left')
            ->join('mode_paiement mp', 'mp.id_mode_paiement = pa.id_mode_paiement', 'left')
            ->join('statut_reservation', 'statut_reservation.id_statut_reservation = r.id_statut_reservation', 'left')
            ->where('r.deleted_at', null);
    }

    /**
     * @return array<string, mixed>
     */
    private function configuration(): array
    {
        $row = db_connect()
            ->table('configuration')
            ->where('deleted_at', null)
            ->orderBy('id_configuration', 'asc')
            ->get()
            ->getRowArray();

        $configuration = is_array($row) ? $row : [];
        $configuration['logo_url'] = $this->assetUrl((string) ($configuration['logo'] ?? 'img/bus.png'));

        return $configuration;
    }

    private function assetUrl(string $path): string
    {
        $path = trim($path);

        if ($path === '') {
            return base_url('img/bus.png');
        }

        if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }

        if (! str_contains($path, '/')) {
            $path = 'img/' . $path;
        }

        return base_url($path);
    }
}
