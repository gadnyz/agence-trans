<?php

namespace App\Controllers\Web;

use CodeIgniter\Database\BaseBuilder;

class ReportController extends BaseWebController
{
    public function index()
    {
        $user = $this->requireAuthApi();

        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $permissions = $user['permissions'] ?? [];
        if (! in_array('*', $permissions, true) && ! in_array('reports.read', $permissions, true)) {
            return redirect()->to($this->roleHomePath())->with('error', 'Accès aux rapports non autorisé.');
        }

        $filters = $this->filters();
        $perPage = $this->perPage();
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $totalReservations = $this->reservationDetailsTotal($filters);
        $totalPages = max(1, (int) ceil($totalReservations / $perPage));
        $page = min($page, $totalPages);

        $userRole = $this->userRole();
        $layout = match($userRole) {
            'super_admin' => 'web/layouts/super_admin',
            'admin'       => 'web/layouts/admin',
            default       => 'web/layouts/super_admin',
        };

        return view('web/super_admin/rapports', [
            'layout' => $layout,
            'title' => 'Rapports',
            'pageTitle' => 'Rapports',
            'reportActionUrl' => base_url($userRole === 'super_admin' ? 'super-admin/rapports' : 'admin/rapports'),
            'user' => $user,
            'filters' => $filters,
            'options' => $this->filterOptions(),
            'summary' => $this->summary($filters),
            'details' => [
                'reservations' => $this->reservationDetails($filters, $page, $perPage),
            ],
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalReservations,
                'total_pages' => $totalPages,
                'from' => $totalReservations === 0 ? 0 : (($page - 1) * $perPage) + 1,
                'to' => min($totalReservations, $page * $perPage),
            ],
        ]);
    }

    public function analyse()
    {
        $user = $this->requireAuthApi();

        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $permissions = $user['permissions'] ?? [];
        if (! in_array('*', $permissions, true) && ! in_array('reports.read', $permissions, true)) {
            return redirect()->to($this->roleHomePath())->with('error', 'Accès aux analyses non autorisé.');
        }

        $userRole = $this->userRole();
        $layout = match($userRole) {
            'super_admin' => 'web/layouts/super_admin',
            'admin'       => 'web/layouts/admin',
            default       => 'web/layouts/super_admin',
        };

        return view('web/super_admin/analyse', [
            'layout' => $layout,
            'title' => 'Analyse',
            'pageTitle' => 'Analyse',
            'user' => $user,
            'filters' => $this->filters(),
            'options' => $this->filterOptions(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function filters(): array
    {
        $today = date('Y-m-d');
        $dateDebut = (string) ($this->request->getGet('date_debut') ?: date('Y-m-01'));
        $dateFin = (string) ($this->request->getGet('date_fin') ?: $today);

        if (! $this->isDate($dateDebut)) {
            $dateDebut = date('Y-m-01');
        }

        if (! $this->isDate($dateFin)) {
            $dateFin = $today;
        }

        if ($dateDebut > $dateFin) {
            [$dateDebut, $dateFin] = [$dateFin, $dateDebut];
        }

        return [
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'id_trajet' => $this->positiveInt('id_trajet'),
            'id_agent' => $this->positiveInt('id_agent'),
        ];
    }

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    private function filterOptions(): array
    {
        return [
            'trajets' => db_connect()
                ->table('trajet t')
                ->select('t.id_trajet, ld.nom_lieu AS lieu_depart, la.nom_lieu AS lieu_arrivee, h.heure_depart, h.heure_arrivee')
                ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
                ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
                ->join('horaire h', 'h.id_horaire = t.id_horaire')
                ->where('t.deleted_at', null)
                ->orderBy('ld.nom_lieu')
                ->get()
                ->getResultArray(),
            'agents' => db_connect()
                ->table('utilisateur')
                ->select('id_utilisateur, username, nom, postnom, prenom')
                ->where('deleted_at', null)
                ->orderBy('username', 'asc')
                ->get()
                ->getResultArray(),
        ];
    }

    /**
     * @return array<string, float|int>
     */
    private function summary(array $filters): array
    {
        $reservation = $this->baseReservationBuilder($filters, false)
            ->select([
                'COUNT(DISTINCT r.id_reservation) AS reservations',
                'COUNT(DISTINCT r.id_client) AS clients',
                'COUNT(DISTINCT p.id_programme) AS courses',
                'COUNT(DISTINCT p.id_bus) AS bus',
                'COUNT(DISTINCT p.id_conducteur) AS conducteurs',
                'COALESCE(SUM(r.nombre_places), 0) AS sieges',
            ])
            ->get()
            ->getRowArray() ?: [];

        $payments = $this->basePaymentBuilder($filters)
            ->select('COALESCE(SUM(pa.montant_paye), 0) AS encaissements, COUNT(pa.id_paiement) AS paiements')
            ->get()
            ->getRowArray() ?: [];

        $newClients = db_connect()
            ->table('client')
            ->select('COUNT(*) AS total')
            ->where('deleted_at', null)
            ->where('DATE(created_at) >=', $filters['date_debut'])
            ->where('DATE(created_at) <=', $filters['date_fin'])
            ->get()
            ->getRowArray();

        return [
            'reservations' => (int) ($reservation['reservations'] ?? 0),
            'clients' => (int) ($reservation['clients'] ?? 0),
            'nouveaux_clients' => (int) ($newClients['total'] ?? 0),
            'courses' => (int) ($reservation['courses'] ?? 0),
            'bus' => (int) ($reservation['bus'] ?? 0),
            'conducteurs' => (int) ($reservation['conducteurs'] ?? 0),
            'sieges' => (int) ($reservation['sieges'] ?? 0),
            'encaissements' => (float) ($payments['encaissements'] ?? 0),
            'paiements' => (int) ($payments['paiements'] ?? 0),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function dailyRevenue(array $filters): array
    {
        return $this->basePaymentBuilder($filters)
            ->select('DATE(pa.date_paiement) AS jour, COUNT(DISTINCT r.id_reservation) AS reservations, COALESCE(SUM(pa.montant_paye), 0) AS encaissements')
            ->groupBy('DATE(pa.date_paiement)')
            ->orderBy('jour', 'asc')
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function paymentModes(array $filters): array
    {
        return $this->basePaymentBuilder($filters)
            ->select('mp.libelle AS mode_paiement, cur.code_currency, COUNT(pa.id_paiement) AS transactions, COALESCE(SUM(pa.montant_paye), 0) AS montant')
            ->join('mode_paiement mp', 'mp.id_mode_paiement = pa.id_mode_paiement')
            ->join('currency cur', 'cur.id_currency = pa.id_currency')
            ->groupBy('mp.libelle, cur.code_currency')
            ->orderBy('montant', 'desc')
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function topClients(array $filters): array
    {
        return $this->baseReservationBuilder($filters, false)
            ->select('cl.id_client, cl.nom, cl.telephone, COUNT(DISTINCT r.id_reservation) AS reservations, COALESCE(SUM(r.nombre_places), 0) AS sieges, COALESCE(SUM(r.montant_final), 0) AS montant')
            ->groupBy('cl.id_client, cl.nom, cl.telephone')
            ->orderBy('montant', 'desc')
            ->limit(20)
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function routesReport(array $filters): array
    {
        return $this->baseReservationBuilder($filters, false)
            ->select([
                't.id_trajet',
                'CONCAT(ld.nom_lieu, " - ", la.nom_lieu) AS trajet',
                'COUNT(DISTINCT p.id_programme) AS courses',
                'COUNT(DISTINCT r.id_reservation) AS reservations',
                'COALESCE(SUM(r.nombre_places), 0) AS sieges',
                'COALESCE(SUM(r.montant_final), 0) AS montant',
            ])
            ->groupBy('t.id_trajet, ld.nom_lieu, la.nom_lieu')
            ->orderBy('montant', 'desc')
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function programmesReport(array $filters): array
    {
        return $this->baseReservationBuilder($filters, false)
            ->select([
                'p.id_programme',
                'p.date_programme',
                'CONCAT(ld.nom_lieu, " - ", la.nom_lieu) AS trajet',
                'h.heure_depart',
                'b.numero_plaque',
                'COUNT(DISTINCT r.id_reservation) AS reservations',
                'COALESCE(SUM(r.nombre_places), 0) AS sieges',
                'COALESCE(SUM(r.montant_final), 0) AS montant',
            ])
            ->groupBy('p.id_programme, p.date_programme, ld.nom_lieu, la.nom_lieu, h.heure_depart, b.numero_plaque')
            ->orderBy('p.date_programme', 'desc')
            ->limit(50)
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function driversReport(array $filters): array
    {
        return $this->baseReservationBuilder($filters, false)
            ->select([
                'cond.id_conducteur',
                'TRIM(CONCAT(COALESCE(cond.nom, ""), " ", COALESCE(cond.postnom, ""), " ", COALESCE(cond.prenom, ""))) AS conducteur',
                'COUNT(DISTINCT p.id_programme) AS courses',
                'COUNT(DISTINCT r.id_reservation) AS reservations',
                'COALESCE(SUM(r.nombre_places), 0) AS sieges',
                'COALESCE(SUM(r.montant_final), 0) AS montant',
            ])
            ->groupBy('cond.id_conducteur, cond.nom, cond.postnom, cond.prenom')
            ->orderBy('courses', 'desc')
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function busesReport(array $filters): array
    {
        return $this->baseReservationBuilder($filters, false)
            ->select([
                'b.id_bus',
                'b.numero_plaque',
                'b.nombre_places AS capacite',
                'COUNT(DISTINCT p.id_programme) AS courses',
                'COUNT(DISTINCT r.id_reservation) AS reservations',
                'COALESCE(SUM(r.nombre_places), 0) AS passagers',
                'COALESCE(SUM(r.montant_final), 0) AS montant',
            ])
            ->groupBy('b.id_bus, b.numero_plaque, b.nombre_places')
            ->orderBy('passagers', 'desc')
            ->get()
            ->getResultArray();
    }

    /**
     * @return array<string, mixed>
     */
    private function paymentPivotByDay(array $filters): array
    {
        $rows = $this->basePaymentBuilder($filters)
            ->select('DATE(pa.date_paiement) AS ligne, CONCAT(mp.libelle, " (", cur.code_currency, ")") AS colonne, COALESCE(SUM(pa.montant_paye), 0) AS valeur', false)
            ->join('mode_paiement mp', 'mp.id_mode_paiement = pa.id_mode_paiement')
            ->join('currency cur', 'cur.id_currency = pa.id_currency')
            ->groupBy('DATE(pa.date_paiement), mp.libelle, cur.code_currency', false)
            ->orderBy('DATE(pa.date_paiement)', 'asc', false)
            ->orderBy('mp.libelle', 'asc')
            ->get()
            ->getResultArray();

        return $this->matrix($rows, 'money', false);
    }

    /**
     * @return array<string, mixed>
     */
    private function paymentPivotByRoute(array $filters): array
    {
        $rows = $this->basePaymentBuilder($filters)
            ->select('CONCAT(ld.nom_lieu, " - ", la.nom_lieu) AS ligne, CONCAT(mp.libelle, " (", cur.code_currency, ")") AS colonne, COALESCE(SUM(pa.montant_paye), 0) AS valeur', false)
            ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
            ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
            ->join('mode_paiement mp', 'mp.id_mode_paiement = pa.id_mode_paiement')
            ->join('currency cur', 'cur.id_currency = pa.id_currency')
            ->groupBy('t.id_trajet, ld.nom_lieu, la.nom_lieu, mp.libelle, cur.code_currency', false)
            ->orderBy('valeur', 'desc')
            ->get()
            ->getResultArray();

        return $this->matrix($rows, 'money');
    }

    /**
     * @return array<string, mixed>
     */
    private function reservationPivotByDriverRoute(array $filters): array
    {
        $rows = $this->baseReservationBuilder($filters, false)
            ->select('TRIM(CONCAT(COALESCE(cond.nom, ""), " ", COALESCE(cond.postnom, ""), " ", COALESCE(cond.prenom, ""))) AS ligne, CONCAT(ld.nom_lieu, " - ", la.nom_lieu) AS colonne, COALESCE(SUM(r.nombre_places), 0) AS valeur', false)
            ->groupBy('cond.id_conducteur, cond.nom, cond.postnom, cond.prenom, t.id_trajet, ld.nom_lieu, la.nom_lieu', false)
            ->orderBy('ligne', 'asc')
            ->get()
            ->getResultArray();

        return $this->matrix($rows, 'number');
    }

    /**
     * @return array<string, mixed>
     */
    private function reservationPivotByBusRoute(array $filters): array
    {
        $rows = $this->baseReservationBuilder($filters, false)
            ->select('b.numero_plaque AS ligne, CONCAT(ld.nom_lieu, " - ", la.nom_lieu) AS colonne, COALESCE(SUM(r.nombre_places), 0) AS valeur', false)
            ->groupBy('b.id_bus, b.numero_plaque, t.id_trajet, ld.nom_lieu, la.nom_lieu', false)
            ->orderBy('b.numero_plaque', 'asc')
            ->get()
            ->getResultArray();

        return $this->matrix($rows, 'number');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function reservationDetails(array $filters, int $page, int $perPage): array
    {
        $paidExpression = 'COALESCE(SUM(CASE WHEN pa.statut_paiement = "Valide" THEN pa.montant_paye ELSE 0 END), 0)';

        return $this->baseReservationBuilder($filters, true)
            ->select([
                'r.id_reservation',
                'r.reference_reservation',
                'DATE(r.date_reservation) AS date_reservation',
                'p.id_programme',
                'p.date_programme',
                'CONCAT(ld.nom_lieu, " - ", la.nom_lieu) AS trajet',
                'h.heure_depart',
                'cl.nom AS client',
                'cl.telephone',
                'r.nombre_places',
                $paidExpression . ' AS montant_paye',
                'GROUP_CONCAT(DISTINCT mp.libelle ORDER BY mp.libelle SEPARATOR ", ") AS modes_paiement',
                'sr.libelle AS statut_reservation',
                'b.numero_plaque',
                'TRIM(CONCAT(COALESCE(cond.nom, ""), " ", COALESCE(cond.postnom, ""), " ", COALESCE(cond.prenom, ""))) AS conducteur',
                'COALESCE(u.username, "-") AS agent',
            ], false)
            ->join('mode_paiement mp', 'mp.id_mode_paiement = pa.id_mode_paiement', 'left')
            ->join('utilisateur u', 'u.id_utilisateur = r.created_by', 'left')
            ->groupBy('r.id_reservation, r.reference_reservation, r.date_reservation, p.id_programme, p.date_programme, ld.nom_lieu, la.nom_lieu, h.heure_depart, cl.nom, cl.telephone, r.nombre_places, sr.libelle, b.numero_plaque, cond.nom, cond.postnom, cond.prenom, u.username', false)
            ->orderBy('r.date_reservation', 'desc')
            ->orderBy('r.id_reservation', 'desc')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();
    }

    private function reservationDetailsTotal(array $filters): int
    {
        $row = $this->baseReservationBuilder($filters, false)
            ->select('COUNT(DISTINCT r.id_reservation) AS total')
            ->get()
            ->getRowArray();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function incompletePayments(array $filters): array
    {
        $paidExpression = 'COALESCE(SUM(CASE WHEN pa.statut_paiement = "Valide" THEN pa.montant_paye / COALESCE(NULLIF(pa.taux_conversion, 0), 1) ELSE 0 END), 0)';

        return $this->baseReservationBuilder($filters, true)
            ->select([
                'r.reference_reservation',
                'DATE(r.date_reservation) AS date_reservation',
                'cl.nom AS client',
                'cl.telephone',
                'r.montant_final AS total_du',
                'cur.code_currency',
                $paidExpression . ' AS total_recouvre',
                'r.montant_final - ' . $paidExpression . ' AS reste_a_payer',
            ], false)
            ->join('currency cur', 'cur.id_currency = r.id_currency')
            ->groupBy('r.id_reservation, r.reference_reservation, r.date_reservation, cl.nom, cl.telephone, r.montant_final, cur.code_currency', false)
            ->having('reste_a_payer >', 0.01)
            ->orderBy('reste_a_payer', 'desc')
            ->limit(50)
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fleetUtilization(array $filters): array
    {
        return $this->baseReservationBuilder($filters, false)
            ->select('b.id_bus, b.numero_plaque, b.nombre_places AS capacite, COUNT(DISTINCT p.id_programme) AS courses, COALESCE(SUM(r.nombre_places), 0) AS passagers, ROUND(COALESCE(SUM(r.nombre_places) / NULLIF(COUNT(DISTINCT p.id_programme) * b.nombre_places, 0) * 100, 0), 1) AS taux_remplissage', false)
            ->groupBy('b.id_bus, b.numero_plaque, b.nombre_places', false)
            ->orderBy('taux_remplissage', 'desc')
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function demandByWeekday(array $filters): array
    {
        $labels = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $rows = $this->baseReservationBuilder($filters, false)
            ->select('WEEKDAY(r.date_reservation) AS jour_index, COUNT(DISTINCT r.id_reservation) AS reservations, COALESCE(SUM(r.nombre_places), 0) AS sieges', false)
            ->groupBy('WEEKDAY(r.date_reservation)', false)
            ->orderBy('jour_index', 'asc')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['jour'] = $labels[(int) ($row['jour_index'] ?? 0)] ?? 'Jour';
        }
        unset($row);

        return $rows;
    }

    /**
     * @param list<array<string, mixed>> $rows
     *
     * @return array<string, mixed>
     */
    private function matrix(array $rows, string $valueType, bool $sortRowsByTotal = true): array
    {
        $columns = [];
        $data = [];
        $columnTotals = [];
        $grandTotal = 0.0;

        foreach ($rows as $row) {
            $line = trim((string) ($row['ligne'] ?? '')) ?: 'Non défini';
            $column = trim((string) ($row['colonne'] ?? '')) ?: 'Non défini';
            $value = (float) ($row['valeur'] ?? 0);

            if (! in_array($column, $columns, true)) {
                $columns[] = $column;
                $columnTotals[$column] = 0.0;
            }

            if (! isset($data[$line])) {
                $data[$line] = [
                    'label' => $line,
                    'cells' => [],
                    'total' => 0.0,
                ];
            }

            $data[$line]['cells'][$column] = ($data[$line]['cells'][$column] ?? 0.0) + $value;
            $data[$line]['total'] += $value;
            $columnTotals[$column] += $value;
            $grandTotal += $value;
        }

        if ($sortRowsByTotal) {
            uasort($data, static fn (array $left, array $right): int => $right['total'] <=> $left['total']);
        }

        return [
            'columns' => $columns,
            'rows' => array_values($data),
            'columnTotals' => $columnTotals,
            'grandTotal' => $grandTotal,
            'valueType' => $valueType,
        ];
    }

    private function baseReservationBuilder(array $filters, bool $joinPayments): BaseBuilder
    {
        $builder = db_connect()
            ->table('reservation r')
            ->join('programme p', 'p.id_programme = r.id_programme')
            ->join('trajet t', 't.id_trajet = p.id_trajet')
            ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
            ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
            ->join('horaire h', 'h.id_horaire = t.id_horaire')
            ->join('bus b', 'b.id_bus = p.id_bus')
            ->join('conducteur cond', 'cond.id_conducteur = p.id_conducteur')
            ->join('client cl', 'cl.id_client = r.id_client')
            ->join('statut_reservation sr', 'sr.id_statut_reservation = r.id_statut_reservation')
            ->where('r.deleted_at', null)
            ->where('p.deleted_at', null)
            ->where('sr.libelle !=', 'ANNULE');

        if ($joinPayments) {
            $builder->join('paiement pa', 'pa.id_reservation = r.id_reservation AND pa.deleted_at IS NULL', 'left');
        }

        return $this->applyFilters($builder, $filters);
    }

    private function basePaymentBuilder(array $filters): BaseBuilder
    {
        $builder = db_connect()
            ->table('paiement pa')
            ->join('reservation r', 'r.id_reservation = pa.id_reservation')
            ->join('statut_reservation sr', 'sr.id_statut_reservation = r.id_statut_reservation')
            ->join('programme p', 'p.id_programme = r.id_programme')
            ->join('trajet t', 't.id_trajet = p.id_trajet')
            ->join('bus b', 'b.id_bus = p.id_bus')
            ->join('conducteur cond', 'cond.id_conducteur = p.id_conducteur')
            ->join('client cl', 'cl.id_client = r.id_client')
            ->where('pa.deleted_at', null)
            ->where('r.deleted_at', null)
            ->where('p.deleted_at', null)
            ->where('sr.libelle !=', 'ANNULE')
            ->where('pa.statut_paiement', 'Valide');

        return $this->applyFilters($builder, $filters);
    }

    private function applyFilters(BaseBuilder $builder, array $filters): BaseBuilder
    {
        $dateField = 'r.date_reservation';
        $builder->where('DATE(' . $dateField . ') >=', $filters['date_debut']);
        $builder->where('DATE(' . $dateField . ') <=', $filters['date_fin']);

        foreach ([
            'id_trajet' => 'p.id_trajet',
            'id_agent' => 'r.created_by',
        ] as $filter => $column) {
            if ($filters[$filter]) {
                $builder->where($column, $filters[$filter]);
            }
        }

        return $builder;
    }

    private function perPage(): int
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 25);

        return max(10, min(100, $perPage));
    }

    private function positiveInt(string $key): ?int
    {
        $value = (int) ($this->request->getGet($key) ?? 0);

        return $value > 0 ? $value : null;
    }

    private function isDate(string $date): bool
    {
        $value = \DateTimeImmutable::createFromFormat('Y-m-d', $date);

        return $value !== false && $value->format('Y-m-d') === $date;
    }
}
