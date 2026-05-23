<?php

namespace App\Controllers\Api;

use App\Api\BaseApiController;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\HTTP\ResponseInterface;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Throwable;

class PlanningController extends BaseApiController
{
    public function listProgrammes(): ResponseInterface
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = max(1, min(100, (int) ($this->request->getGet('per_page') ?? 25)));
        $builder = $this->programmeBuilder();

        foreach (['id_bus', 'id_conducteur', 'id_trajet', 'statut'] as $filter) {
            $value = $this->request->getGet($filter);

            if ($value !== null && $value !== '') {
                $builder->where('p.' . $filter, $value);
            }
        }

        $dateDebut = (string) ($this->request->getGet('date_debut') ?? '');
        $dateFin = (string) ($this->request->getGet('date_fin') ?? '');

        if ($dateDebut !== '') {
            $builder->where('p.date_programme >=', $dateDebut);
        }

        if ($dateFin !== '') {
            $builder->where('p.date_programme <=', $dateFin);
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();
        $items = $builder
            ->orderBy('p.date_programme', 'asc')
            ->orderBy('h.heure_depart', 'asc')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        return $this->success([
            'items' => $items,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    public function showProgramme(int $id): ResponseInterface
    {
        $row = $this->programmeBuilder()
            ->where('p.id_programme', $id)
            ->get()
            ->getRowArray();

        if (! is_array($row)) {
            return $this->failure('Programme introuvable.', ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->success($row);
    }

    public function calendarProgrammes(): ResponseInterface
    {
        $start = (string) ($this->request->getGet('start') ?? $this->request->getGet('date_debut') ?? '');
        $end = (string) ($this->request->getGet('end') ?? $this->request->getGet('date_fin') ?? '');
        $builder = $this->programmeBuilder();

        if ($start !== '') {
            $builder->where('p.date_programme >=', substr($start, 0, 10));
        }

        if ($end !== '') {
            $builder->where('p.date_programme <=', substr($end, 0, 10));
        }

        $rows = $builder
            ->orderBy('p.date_programme', 'asc')
            ->orderBy('h.heure_depart', 'asc')
            ->get()
            ->getResultArray();

        $events = array_map([$this, 'calendarEvent'], $rows);

        return $this->response
            ->setStatusCode(ResponseInterface::HTTP_OK)
            ->setJSON($events);
    }

    public function createProgrammes(): ResponseInterface
    {
        $payload = $this->payload();
        $errors = $this->validatePlanningPayload($payload);

        if ($errors !== []) {
            return $this->failure('Validation echouee.', ResponseInterface::HTTP_BAD_REQUEST, $errors);
        }

        $dateDebut = new DateTimeImmutable((string) $payload['date_debut']);
        $dateFin = new DateTimeImmutable((string) $payload['date_fin']);

        if ($dateDebut > $dateFin) {
            return $this->failure('La date de debut doit etre inferieure ou egale a la date de fin.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        if ($dateDebut < $this->today()) {
            return $this->failure('Impossible de planifier avant la date actuelle.', ResponseInterface::HTTP_BAD_REQUEST, [
                'date_debut' => 'La date de debut ne peut pas etre anterieure a aujourd hui.',
            ]);
        }

        if ((int) $dateDebut->diff($dateFin)->format('%a') > 366) {
            return $this->failure('La plage de planification ne peut pas depasser 366 jours.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $db = db_connect();
        $bus = $this->findActiveRow('bus', 'id_bus', (int) $payload['id_bus']);
        $conducteur = $this->findActiveRow('conducteur', 'id_conducteur', (int) $payload['id_conducteur']);
        $trajet = $this->findActiveRow('trajet', 'id_trajet', (int) $payload['id_trajet']);

        $missing = [];

        if ($bus === null) {
            $missing['id_bus'] = 'Bus introuvable.';
        }

        if ($conducteur === null) {
            $missing['id_conducteur'] = 'Conducteur introuvable.';
        }

        if ($trajet === null) {
            $missing['id_trajet'] = 'Trajet introuvable.';
        }

        if ($missing !== []) {
            return $this->failure('Reference introuvable.', ResponseInterface::HTTP_BAD_REQUEST, $missing);
        }

        $dates = $this->selectedDates($dateDebut, $dateFin, $payload['jours_semaine'] ?? []);

        if ($dates === []) {
            return $this->failure('Aucune date selectionnee dans la plage.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $conflicts = $this->conflictsForDates(
            $dates,
            (int) $payload['id_bus'],
            (int) $payload['id_conducteur'],
            (int) $payload['id_trajet']
        );

        if ($conflicts !== []) {
            return $this->failure('Conflit de planification.', ResponseInterface::HTTP_CONFLICT, [
                'conflicts' => $conflicts,
            ]);
        }

        $placesDisponibles = (int) ($payload['places_disponibles'] ?? $bus['nombre_places'] ?? 0);
        $statut = trim((string) ($payload['statut'] ?? 'Planifie'));
        $statut = $statut !== '' ? $statut : 'Planifie';
        $rows = [];

        foreach ($dates as $date) {
            $rows[] = [
                'id_conducteur' => (int) $payload['id_conducteur'],
                'id_trajet' => (int) $payload['id_trajet'],
                'id_bus' => (int) $payload['id_bus'],
                'date_programme' => $date,
                'places_disponibles' => $placesDisponibles,
                'statut' => $statut,
            ];
        }

        try {
            $db->transStart();
            $db->table('programme')->insertBatch($rows);
            $firstId = (int) $db->insertID();
            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failure('Erreur lors de la creation de la planification.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Throwable $e) {
            log_message('error', '[PlanningController] ' . $e->getMessage());

            return $this->failure('Erreur base de donnees.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $created = $this->programmeBuilder()
            ->where('p.id_bus', (int) $payload['id_bus'])
            ->where('p.id_conducteur', (int) $payload['id_conducteur'])
            ->where('p.id_trajet', (int) $payload['id_trajet'])
            ->whereIn('p.date_programme', $dates)
            ->orderBy('p.date_programme', 'asc')
            ->get()
            ->getResultArray();

        return $this->success([
            'created_count' => count($rows),
            'first_id' => $firstId,
            'items' => $created,
        ], 'Planification creee.', ResponseInterface::HTTP_CREATED);
    }

    public function updateProgramme(int $id): ResponseInterface
    {
        $exists = $this->findActiveRow('programme', 'id_programme', $id);

        if ($exists === null) {
            return $this->failure('Programme introuvable.', ResponseInterface::HTTP_NOT_FOUND);
        }

        $payload = $this->payload();
        $payload['date_programme'] = $payload['date_programme'] ?? $payload['date_debut'] ?? null;
        $errors = $this->validateProgrammePayload($payload);

        if ($errors !== []) {
            return $this->failure('Validation echouee.', ResponseInterface::HTTP_BAD_REQUEST, $errors);
        }

        $dateProgramme = new DateTimeImmutable((string) $payload['date_programme']);

        if ($dateProgramme < $this->today()) {
            return $this->failure('Impossible de modifier une planification vers une date passee.', ResponseInterface::HTTP_BAD_REQUEST, [
                'date_programme' => 'La date ne peut pas etre anterieure a aujourd hui.',
            ]);
        }

        $bus = $this->findActiveRow('bus', 'id_bus', (int) $payload['id_bus']);
        $conducteur = $this->findActiveRow('conducteur', 'id_conducteur', (int) $payload['id_conducteur']);
        $trajet = $this->findActiveRow('trajet', 'id_trajet', (int) $payload['id_trajet']);
        $missing = [];

        if ($bus === null) {
            $missing['id_bus'] = 'Bus introuvable.';
        }

        if ($conducteur === null) {
            $missing['id_conducteur'] = 'Conducteur introuvable.';
        }

        if ($trajet === null) {
            $missing['id_trajet'] = 'Trajet introuvable.';
        }

        if ($missing !== []) {
            return $this->failure('Reference introuvable.', ResponseInterface::HTTP_BAD_REQUEST, $missing);
        }

        $conflicts = $this->conflictsForDates(
            [$dateProgramme->format('Y-m-d')],
            (int) $payload['id_bus'],
            (int) $payload['id_conducteur'],
            (int) $payload['id_trajet'],
            $id
        );

        if ($conflicts !== []) {
            return $this->failure('Conflit de planification.', ResponseInterface::HTTP_CONFLICT, [
                'conflicts' => $conflicts,
            ]);
        }

        $placesDisponibles = (int) ($payload['places_disponibles'] ?? $bus['nombre_places'] ?? 0);
        $statut = trim((string) ($payload['statut'] ?? 'Planifie'));

        db_connect()
            ->table('programme')
            ->where('id_programme', $id)
            ->update([
                'id_conducteur' => (int) $payload['id_conducteur'],
                'id_trajet' => (int) $payload['id_trajet'],
                'id_bus' => (int) $payload['id_bus'],
                'date_programme' => $dateProgramme->format('Y-m-d'),
                'places_disponibles' => $placesDisponibles,
                'statut' => $statut !== '' ? $statut : 'Planifie',
            ]);

        $updated = $this->programmeBuilder()
            ->where('p.id_programme', $id)
            ->get()
            ->getRowArray();

        return $this->success($updated, 'Programme mis a jour.');
    }

    public function deleteProgramme(int $id): ResponseInterface
    {
        $exists = db_connect()
            ->table('programme')
            ->where('id_programme', $id)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (! is_array($exists)) {
            return $this->failure('Programme introuvable.', ResponseInterface::HTTP_NOT_FOUND);
        }

        db_connect()
            ->table('programme')
            ->where('id_programme', $id)
            ->update(['deleted_at' => date('Y-m-d H:i:s')]);

        return $this->success(null, 'Programme supprime.');
    }

    private function programmeBuilder(): BaseBuilder
    {
        return db_connect()
            ->table('programme p')
            ->select([
                'p.*',
                'b.numero_plaque',
                'b.nombre_places',
                'c.nom AS conducteur_nom',
                'c.postnom AS conducteur_postnom',
                'c.prenom AS conducteur_prenom',
                'td.nom_lieu AS lieu_depart',
                'ta.nom_lieu AS lieu_arrivee',
                'h.heure_depart',
                'h.heure_arrivee',
                't.prix',
                'cur.code_currency',
            ])
            ->join('bus b', 'b.id_bus = p.id_bus')
            ->join('conducteur c', 'c.id_conducteur = p.id_conducteur')
            ->join('trajet t', 't.id_trajet = p.id_trajet')
            ->join('lieu td', 'td.id_lieu = t.id_lieu_depart')
            ->join('lieu ta', 'ta.id_lieu = t.id_lieu_arrivee')
            ->join('horaire h', 'h.id_horaire = t.id_horaire')
            ->join('currency cur', 'cur.id_currency = t.id_currency')
            ->where('p.deleted_at', null)
            ->where('b.deleted_at', null)
            ->where('c.deleted_at', null)
            ->where('t.deleted_at', null);
    }

    /**
     * @param array<string, mixed> $row
     *
     * @return array<string, mixed>
     */
    private function calendarEvent(array $row): array
    {
        $date = (string) $row['date_programme'];
        $heureDepart = (string) ($row['heure_depart'] ?? '00:00:00');
        $heureArrivee = (string) ($row['heure_arrivee'] ?? $heureDepart);
        $start = new DateTimeImmutable($date . ' ' . $heureDepart);
        $end = new DateTimeImmutable($date . ' ' . $heureArrivee);

        if ($end <= $start) {
            $end = $end->modify('+1 day');
        }

        $conducteur = trim(($row['conducteur_nom'] ?? '') . ' ' . ($row['conducteur_postnom'] ?? '') . ' ' . ($row['conducteur_prenom'] ?? ''));
        $statut = (string) ($row['statut'] ?? 'Planifie');

        return [
            'id' => (string) $row['id_programme'],
            'title' => trim(($row['lieu_depart'] ?? '') . ' - ' . ($row['lieu_arrivee'] ?? '') . ' | ' . ($row['numero_plaque'] ?? '')),
            'start' => $start->format('Y-m-d\TH:i:s'),
            'end' => $end->format('Y-m-d\TH:i:s'),
            'backgroundColor' => $this->eventColor($statut),
            'borderColor' => $this->eventColor($statut),
            'extendedProps' => [
                'date_programme' => $date,
                'statut' => $statut,
                'id_trajet' => (int) $row['id_trajet'],
                'id_bus' => (int) $row['id_bus'],
                'id_conducteur' => (int) $row['id_conducteur'],
                'numero_plaque' => $row['numero_plaque'] ?? null,
                'conducteur' => $conducteur,
                'trajet' => trim(($row['lieu_depart'] ?? '') . ' - ' . ($row['lieu_arrivee'] ?? '')),
                'horaire' => substr($heureDepart, 0, 5) . ' - ' . substr($heureArrivee, 0, 5),
                'places_disponibles' => $row['places_disponibles'] ?? null,
                'prix' => $row['prix'] ?? null,
                'code_currency' => $row['code_currency'] ?? null,
            ],
        ];
    }

    private function eventColor(string $statut): string
    {
        return match (strtolower($statut)) {
            'ouvert' => '#16a34a',
            'suspendu' => '#ca8a04',
            'annule', 'annulé' => '#dc2626',
            'termine', 'terminé' => '#64748b',
            default => '#2563eb',
        };
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, string>
     */
    private function validatePlanningPayload(array $payload): array
    {
        $validation = service('validation');
        $validation->reset();
        $validation->setRules([
            'date_debut' => 'required|valid_date[Y-m-d]',
            'date_fin' => 'required|valid_date[Y-m-d]',
            'id_trajet' => 'required|is_natural_no_zero',
            'id_bus' => 'required|is_natural_no_zero',
            'id_conducteur' => 'required|is_natural_no_zero',
            'places_disponibles' => 'permit_empty|is_natural_no_zero',
            'statut' => 'permit_empty|max_length[50]',
        ]);

        if ($validation->run($payload)) {
            return [];
        }

        return $validation->getErrors();
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, string>
     */
    private function validateProgrammePayload(array $payload): array
    {
        $validation = service('validation');
        $validation->reset();
        $validation->setRules([
            'date_programme' => 'required|valid_date[Y-m-d]',
            'id_trajet' => 'required|is_natural_no_zero',
            'id_bus' => 'required|is_natural_no_zero',
            'id_conducteur' => 'required|is_natural_no_zero',
            'places_disponibles' => 'permit_empty|is_natural_no_zero',
            'statut' => 'permit_empty|max_length[50]',
        ]);

        if ($validation->run($payload)) {
            return [];
        }

        return $validation->getErrors();
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        try {
            $json = $this->request->getJSON(true);
        } catch (Throwable) {
            $json = null;
        }

        if (is_array($json)) {
            return $json;
        }

        return $this->request->getPost();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findActiveRow(string $table, string $primaryKey, int $id): ?array
    {
        $row = db_connect()
            ->table($table)
            ->where($primaryKey, $id)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        return is_array($row) ? $row : null;
    }

    /**
     * @param array<int|string, mixed> $joursSemaine
     *
     * @return list<string>
     */
    private function selectedDates(DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin, array $joursSemaine): array
    {
        $selectedDays = array_values(array_filter(
            array_map('intval', $joursSemaine),
            static fn (int $day): bool => $day >= 1 && $day <= 7
        ));
        $period = new DatePeriod($dateDebut, new DateInterval('P1D'), $dateFin->modify('+1 day'));
        $dates = [];

        foreach ($period as $date) {
            $day = (int) $date->format('N');

            if ($selectedDays !== [] && ! in_array($day, $selectedDays, true)) {
                continue;
            }

            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    /**
     * @param list<string> $dates
     *
     * @return list<array<string, mixed>>
     */
    private function conflictsForDates(array $dates, int $busId, int $conducteurId, int $trajetId, ?int $excludeProgrammeId = null): array
    {
        if ($dates === []) {
            return [];
        }

        $searchDates = [];

        foreach ($dates as $date) {
            $baseDate = new DateTimeImmutable($date);
            $searchDates[] = $baseDate->modify('-1 day')->format('Y-m-d');
            $searchDates[] = $baseDate->format('Y-m-d');
            $searchDates[] = $baseDate->modify('+1 day')->format('Y-m-d');
        }

        $searchDates = array_values(array_unique($searchDates));
        $builder = db_connect()
            ->table('programme p')
            ->select('p.id_programme, p.date_programme, p.id_bus, p.id_conducteur, b.numero_plaque, c.nom AS conducteur_nom, c.postnom AS conducteur_postnom, c.prenom AS conducteur_prenom')
            ->join('bus b', 'b.id_bus = p.id_bus')
            ->join('conducteur c', 'c.id_conducteur = p.id_conducteur')
            ->join('trajet t', 't.id_trajet = p.id_trajet')
            ->join('horaire h', 'h.id_horaire = t.id_horaire')
            ->select('h.heure_depart, h.heure_arrivee')
            ->where('p.deleted_at', null)
            ->whereIn('p.date_programme', $searchDates)
            ->groupStart()
                ->where('p.id_bus', $busId)
                ->orWhere('p.id_conducteur', $conducteurId)
            ->groupEnd()
            ->orderBy('p.date_programme', 'asc');

        if ($excludeProgrammeId !== null) {
            $builder->where('p.id_programme !=', $excludeProgrammeId);
        }

        $candidates = $builder
            ->get()
            ->getResultArray();

        $conflicts = [];

        foreach ($candidates as $candidate) {
            $newWindow = $this->trajetWindowForDate($trajetId, (string) $candidate['date_programme']);
            $existingWindow = $this->timeWindow(
                (string) $candidate['date_programme'],
                (string) $candidate['heure_depart'],
                (string) $candidate['heure_arrivee']
            );

            if ($newWindow !== null && $this->windowsOverlap($newWindow, $existingWindow)) {
                $conflicts[] = $candidate;
            }
        }

        return $conflicts;
    }

    /**
     * @return array{0: DateTimeImmutable, 1: DateTimeImmutable}|null
     */
    private function trajetWindowForDate(int $trajetId, string $date): ?array
    {
        $row = db_connect()
            ->table('trajet t')
            ->select('h.heure_depart, h.heure_arrivee')
            ->join('horaire h', 'h.id_horaire = t.id_horaire')
            ->where('t.id_trajet', $trajetId)
            ->where('t.deleted_at', null)
            ->where('h.deleted_at', null)
            ->get()
            ->getRowArray();

        if (! is_array($row)) {
            return null;
        }

        return $this->timeWindow($date, (string) $row['heure_depart'], (string) $row['heure_arrivee']);
    }

    /**
     * @return array{0: DateTimeImmutable, 1: DateTimeImmutable}
     */
    private function timeWindow(string $date, string $heureDepart, string $heureArrivee): array
    {
        $start = new DateTimeImmutable($date . ' ' . $heureDepart);
        $end = new DateTimeImmutable($date . ' ' . $heureArrivee);

        if ($end <= $start) {
            $end = $end->modify('+1 day');
        }

        return [$start, $end];
    }

    /**
     * @param array{0: DateTimeImmutable, 1: DateTimeImmutable} $left
     * @param array{0: DateTimeImmutable, 1: DateTimeImmutable} $right
     */
    private function windowsOverlap(array $left, array $right): bool
    {
        return $left[0] < $right[1] && $left[1] > $right[0];
    }

    private function today(): DateTimeImmutable
    {
        return new DateTimeImmutable(date('Y-m-d'));
    }
}
