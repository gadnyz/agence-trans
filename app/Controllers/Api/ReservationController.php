<?php

namespace App\Controllers\Api;

use App\Api\BaseApiController;
use App\Services\Payment\PaymentGatewayManager;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\HTTP\ResponseInterface;
use DateTimeImmutable;
use Throwable;

class ReservationController extends BaseApiController
{
    public function programmesDisponibles(): ResponseInterface
    {
        $date = trim((string) $this->request->getGet('date'));

        if (! $this->isDate($date)) {
            return $this->invalidDateResponse();
        }

        $rows = $this->programmeBuilder()
            ->where('p.date_programme', $date)
            ->where('p.places_disponibles >', 0)
            ->whereNotIn('p.statut', ['Annule', 'Annulee', 'Termine', 'Terminee', 'En cours'])
            ->orderBy('h.heure_depart', 'asc')
            ->get()
            ->getResultArray();

        return $this->success([
            'items' => $rows,
        ]);
    }

    public function programmesManifestes(): ResponseInterface
    {
        $date = trim((string) $this->request->getGet('date'));

        if (! $this->isDate($date)) {
            return $this->invalidDateResponse();
        }

        $rows = $this->programmeBuilder()
            ->where('p.date_programme', $date)
            ->orderBy('h.heure_depart', 'asc')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['places_disponibles'] = max(0, (int) ($row['places_disponibles'] ?? 0));
            $row['nombre_places'] = (int) ($row['nombre_places'] ?? 0);
            $row['places_occupees'] = max(0, $row['nombre_places'] - $row['places_disponibles']);
            $row['is_complet'] = $row['places_disponibles'] <= 0;
            $row['manifeste_url'] = base_url('programmes/' . (int) $row['id_programme'] . '/manifeste');
        }
        unset($row);

        return $this->success([
            'items' => $rows,
        ]);
    }

    public function arretsProgramme(int $idProgramme): ResponseInterface
    {
        $programme = $this->findProgramme($idProgramme);

        if ($programme === null) {
            return $this->failure('Programme introuvable.', ResponseInterface::HTTP_NOT_FOUND);
        }

        $rows = db_connect()
            ->table('arret a')
            ->select('a.id_arret, a.id_lieu, a.ordre_arret, a.temps_estime, l.nom_lieu, l.province, l.pays')
            ->join('lieu l', 'l.id_lieu = a.id_lieu')
            ->where('a.id_trajet', (int) $programme['id_trajet'])
            ->where('a.deleted_at', null)
            ->where('l.deleted_at', null)
            ->orderBy('a.ordre_arret', 'asc')
            ->get()
            ->getResultArray();

        return $this->success([
            'items' => $rows,
        ]);
    }

    public function clientByPhone(): ResponseInterface
    {
        $telephone = $this->normalizePhone((string) $this->request->getGet('telephone'));

        if ($telephone === '') {
            return $this->failure('Telephone requis.', ResponseInterface::HTTP_BAD_REQUEST, [
                'telephone' => 'Indiquer le numero de telephone.',
            ]);
        }

        $client = $this->findClientByPhone($telephone);

        return $this->success([
            'client' => $client,
            'exists' => $client !== null,
        ]);
    }

    public function create(): ResponseInterface
    {
        $payload = $this->payload();
        $errors = $this->validateCreatePayload($payload);

        if ($errors !== []) {
            return $this->failure('Validation echouee.', ResponseInterface::HTTP_BAD_REQUEST, $errors);
        }

        $programme = $this->findProgramme((int) $payload['id_programme']);

        if ($programme === null) {
            return $this->failure('Programme introuvable.', ResponseInterface::HTTP_NOT_FOUND);
        }

        if (! $this->programmeReservable($programme)) {
            return $this->failure('Ce programme ne peut pas recevoir de reservation.', ResponseInterface::HTTP_CONFLICT);
        }

        $nombrePlaces = (int) $payload['nombre_places'];

        if ((int) $programme['places_disponibles'] < $nombrePlaces) {
            return $this->failure('Places insuffisantes.', ResponseInterface::HTTP_CONFLICT, [
                'places_disponibles' => (int) $programme['places_disponibles'],
            ]);
        }

        $lieuReservation = $this->validatedLieuReservation($payload, $programme);

        if ($lieuReservation === false) {
            return $this->failure('Lieu de descente invalide pour ce trajet.', ResponseInterface::HTTP_BAD_REQUEST, [
                'id_lieu_reservation' => 'Sélectionner un arrêt existant sur le trajet.',
            ]);
        }

        $mode = $this->findModePaiement((int) $payload['id_mode_paiement']);

        if ($mode === null) {
            return $this->failure('Mode de paiement introuvable.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $clientPayload = $this->clientPayload($payload);
        $paymentPayload = is_array($payload['payment'] ?? null) ? $payload['payment'] : [];
        $montantFinal = (float) $programme['prix'] * $nombrePlaces;
        $paymentContext = [
            'id_programme' => (int) $programme['id_programme'],
            'id_mode_paiement' => (int) $mode['id_mode_paiement'],
            'mode_paiement' => $mode['libelle'],
            'montant' => $montantFinal,
            'id_currency' => (int) $programme['id_currency'],
            'code_currency' => $programme['code_currency'],
        ];
        $paymentResult = (new PaymentGatewayManager())
            ->gatewayForMode((string) $mode['libelle'])
            ->confirm($paymentContext, $paymentPayload);

        if (! $paymentResult->success) {
            return $this->failure($paymentResult->message, ResponseInterface::HTTP_PAYMENT_REQUIRED);
        }

        $db = db_connect();

        try {
            $db->transStart();

            // $client = $this->ensureClient($clientPayload);
            if (!empty($payload['id_client'])) {
    // Si JavaScript a envoyé un ID, on l'utilise directement
    $idClientToUse = (int) $payload['id_client'];
} else {
    // Sinon (ex: API appelée par un autre système), on cherche/crée avec le téléphone
    $client = $this->ensureClient($clientPayload);
    $idClientToUse = (int) $client['id_client'];
}

            $waitingStatusId = $this->statusId('EN_ATTENTE') ?? $this->firstStatusId();

            $db->table('reservation')->insert([
                'id_programme' => (int) $programme['id_programme'],
                // 'id_client' => (int) $client['id_client'],
                'id_client' => $idClientToUse,
                'created_by' => $this->currentUserId(),
                'id_statut_reservation' => $waitingStatusId,
                'date_reservation' => date('Y-m-d H:i:s'),
                'id_lieu_reservation' => $lieuReservation,
                'nombre_places' => $nombrePlaces,
                'id_currency' => (int) $programme['id_currency'],
            ]);
            $reservationId = (int) $db->insertID();

            $db->table('paiement')->insert([
                'id_reservation' => $reservationId,
                'id_mode_paiement' => (int) $mode['id_mode_paiement'],
                'montant_paye' => $montantFinal,
                'id_currency' => (int) $programme['id_currency'],
                'taux_conversion' => 1,
                'reference_paiement' => $paymentResult->reference,
                'date_paiement' => date('Y-m-d H:i:s'),
                'statut_paiement' => 'Valide',
            ]);

            // NOUVEAU : Mise à jour des places disponibles
            $db->table('programme')
               ->where('id_programme', (int) $programme['id_programme'])
               ->set('places_disponibles', 'places_disponibles - ' . $nombrePlaces, false) // false empêche CI d'échapper la soustraction
               ->update();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failure('Reservation impossible.', ResponseInterface::HTTP_CONFLICT);
            }
        } catch (Throwable $e) {
            $db->transRollback();

            return $this->databaseFailure($e);
        }

        return $this->success([
            'reservation' => $this->reservationDetail($reservationId),
            'ticket_url' => base_url('reservations/' . $reservationId . '/ticket'),
            'manifeste_url' => base_url('programmes/' . (int) $programme['id_programme'] . '/manifeste'),
        ], 'Reservation validee.', ResponseInterface::HTTP_CREATED);
    }

    public function show($id = null): ResponseInterface
    {
        $row = $this->reservationDetail((int) $id);

        if ($row === null) {
            return $this->failure('Reservation introuvable.', ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->success($row);
    }

    public function list(): ResponseInterface
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = max(1, min(100, (int) ($this->request->getGet('per_page') ?? 25)));
        $builder = $this->reservationBuilder();

        // Filtre par date programme
        $dateDebut = trim((string) ($this->request->getGet('date_debut') ?? $this->request->getGet('date') ?? ''));
        $dateFin   = trim((string) ($this->request->getGet('date_fin')   ?? $this->request->getGet('date') ?? ''));

        if ($this->isDate($dateDebut)) {
            $builder->where('p.date_programme >=', $dateDebut);
        }
        if ($this->isDate($dateFin)) {
            $builder->where('p.date_programme <=', $dateFin);
        }

        // Recherche texte (nom client, téléphone, référence)
        $search = trim((string) ($this->request->getGet('search') ?? ''));
        if ($search !== '') {
            $builder->groupStart()
                ->like('cl.nom', $search)
                ->orLike('cl.telephone', $search)
                ->orLike('r.reference_reservation', $search)
                ->groupEnd();
        }

        // Filtre par statut
        $statut = trim((string) ($this->request->getGet('statut') ?? ''));
        if ($statut !== '') {
            $builder->where('statut_reservation.libelle', $statut);
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();
        $items = $builder
            ->orderBy('r.date_reservation', 'desc')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        return $this->success([
            'items' => $items,
            'meta' => [
                'page'        => $page,
                'per_page'    => $perPage,
                'total'       => $total,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    public function update($id = null): ResponseInterface
    {
        $row = $this->reservationDetail((int) $id);
        if ($row === null) {
            return $this->failure('Réservation introuvable.', ResponseInterface::HTTP_NOT_FOUND);
        }

        // On ne peut pas modifier une réservation annulée
        $statutActuel = strtolower((string) ($row['statut_reservation'] ?? ''));
        if (in_array($statutActuel, ['annule', 'annulee', 'annulé', 'annulée'], true)) {
            return $this->failure('Impossible de modifier une réservation annulée.', ResponseInterface::HTTP_CONFLICT);
        }

        $payload = $this->payload();
        $db      = db_connect();

        // Champs modifiables
        $updates = [];

        // Nouveau statut
        if (!empty($payload['id_statut_reservation'])) {
            $updates['id_statut_reservation'] = (int) $payload['id_statut_reservation'];
        }

        // Nouveau nombre de places
        if (!empty($payload['nombre_places'])) {
            $nouveauNb = (int) $payload['nombre_places'];
            $ancienNb  = (int) $row['nombre_places'];
            $diff      = $nouveauNb - $ancienNb;

            if ($diff !== 0) {
                // Vérifier les places disponibles si on augmente
                if ($diff > 0) {
                    $programme = db_connect()->table('programme')
                        ->where('id_programme', (int) $row['id_programme'])
                        ->get()->getRowArray();
                    $dispo = (int) ($programme['places_disponibles'] ?? 0);
                    if ($dispo < $diff) {
                        return $this->failure('Places insuffisantes (' . $dispo . ' disponible(s)).', ResponseInterface::HTTP_CONFLICT);
                    }
                    // Réduire les places dispo
                    $db->table('programme')
                        ->where('id_programme', (int) $row['id_programme'])
                        ->set('places_disponibles', 'places_disponibles - ' . $diff, false)
                        ->update();
                } else {
                    // Libérer les places
                    $db->table('programme')
                        ->where('id_programme', (int) $row['id_programme'])
                        ->set('places_disponibles', 'places_disponibles + ' . abs($diff), false)
                        ->update();
                }
            }
            $updates['nombre_places'] = $nouveauNb;
        }

        // Nouveau lieu de réservation
        if (array_key_exists('id_lieu_reservation', $payload)) {
            $idLieu = (int) ($payload['id_lieu_reservation'] ?? 0);
            $updates['id_lieu_reservation'] = $idLieu > 0 ? $idLieu : null;
        }

        if (empty($updates)) {
            return $this->failure('Aucune modification fournie.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $updates['updated_at'] = date('Y-m-d H:i:s');

        try {
            $db->table('reservation')
                ->where('id_reservation', (int) $id)
                ->update($updates);
        } catch (Throwable $e) {
            return $this->databaseFailure($e);
        }

        return $this->success([
            'reservation' => $this->reservationDetail((int) $id),
        ], 'Réservation mise à jour.');
    }

    public function cancel($id = null): ResponseInterface
    {
        $row = $this->reservationDetail((int) $id);
        if ($row === null) {
            return $this->failure('Réservation introuvable.', ResponseInterface::HTTP_NOT_FOUND);
        }

        $statutActuel = strtolower((string) ($row['statut_reservation'] ?? ''));
        if (in_array($statutActuel, ['annule', 'annulee', 'annulé', 'annulée'], true)) {
            return $this->failure('Cette réservation est déjà annulée.', ResponseInterface::HTTP_CONFLICT);
        }

        $db      = db_connect();
        $payload = $this->payload();
        $motif   = trim((string) ($payload['motif'] ?? ''));

        // Trouver le statut "annulé"
        $cancelStatusId = $this->statusId('ANNULE')
            ?? $this->statusId('ANNULEE')
            ?? $this->statusId('Annule')
            ?? $this->statusId('Annulé')
            ?? $this->statusId('Annulée');

        try {
            $db->transStart();

            // Marquer la réservation comme annulée
            $db->table('reservation')
                ->where('id_reservation', (int) $id)
                ->update([
                    'id_statut_reservation' => $cancelStatusId,
                    'motif_annulation'       => $motif ?: null,
                    'updated_at'             => date('Y-m-d H:i:s'),
                ]);

            // Restaurer les places disponibles
            $nbPlaces = (int) ($row['nombre_places'] ?? 1);
            $db->table('programme')
                ->where('id_programme', (int) $row['id_programme'])
                ->set('places_disponibles', 'places_disponibles + ' . $nbPlaces, false)
                ->update();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failure('Annulation impossible.', ResponseInterface::HTTP_CONFLICT);
            }
        } catch (Throwable $e) {
            $db->transRollback();
            return $this->databaseFailure($e);
        }

        return $this->success([
            'reservation' => $this->reservationDetail((int) $id),
        ], 'Réservation annulée.');
    }

    public function delete($id = null): ResponseInterface
    {
        $row = $this->reservationDetail((int) $id);
        if ($row === null) {
            return $this->failure('Réservation introuvable.', ResponseInterface::HTTP_NOT_FOUND);
        }

        $db = db_connect();

        try {
            $db->transStart();

            // Marquer la réservation comme supprimée (deleted_at)
            $db->table('reservation')
                ->where('id_reservation', (int) $id)
                ->update([
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

            // Si elle n'était pas déjà annulée, restaurer les places disponibles
            $statutActuel = strtolower((string) ($row['statut_reservation'] ?? ''));
            if (!in_array($statutActuel, ['annule', 'annulee', 'annulé', 'annulée'], true)) {
                $nbPlaces = (int) ($row['nombre_places'] ?? 1);
                $db->table('programme')
                    ->where('id_programme', (int) $row['id_programme'])
                    ->set('places_disponibles', 'places_disponibles + ' . $nbPlaces, false)
                    ->update();
            }

            // Supprimer aussi le paiement associé
            $db->table('paiement')
                ->where('id_reservation', (int) $id)
                ->update([
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failure('Suppression impossible.', ResponseInterface::HTTP_CONFLICT);
            }
        } catch (Throwable $e) {
            $db->transRollback();
            return $this->databaseFailure($e);
        }

        return $this->success(null, 'Réservation supprimée.');
    }

    public function addPayment($id = null): ResponseInterface
    {
        $row = $this->reservationDetail((int) $id);
        if ($row === null) {
            return $this->failure('Réservation introuvable.', ResponseInterface::HTTP_NOT_FOUND);
        }

        $payload = $this->payload();
        $montant = (float) ($payload['montant_paye'] ?? 0);
        $idMode  = (int) ($payload['id_mode_paiement'] ?? 0);

        if ($montant <= 0) {
            return $this->failure('Le montant doit être supérieur à 0.', ResponseInterface::HTTP_BAD_REQUEST);
        }
        if ($idMode <= 0) {
            return $this->failure('Mode de paiement requis.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $mode = $this->findModePaiement($idMode);
        if ($mode === null) {
            return $this->failure('Mode de paiement introuvable.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $db = db_connect();
        try {
            // Supprimer l'ancien paiement si existe
            $db->table('paiement')
                ->where('id_reservation', (int) $id)
                ->set('deleted_at', date('Y-m-d H:i:s'))
                ->update();

            // Insérer le nouveau paiement
            $db->table('paiement')->insert([
                'id_reservation'    => (int) $id,
                'id_mode_paiement'  => $idMode,
                'montant_paye'      => $montant,
                'id_currency'       => (int) $row['id_currency'],
                'taux_conversion'   => 1,
                'reference_paiement'=> trim((string) ($payload['reference_paiement'] ?? '')),
                'date_paiement'     => date('Y-m-d H:i:s'),
                'statut_paiement'   => 'Valide',
            ]);
        } catch (Throwable $e) {
            return $this->databaseFailure($e);
        }

        return $this->success([
            'reservation' => $this->reservationDetail((int) $id),
        ], 'Paiement enregistré.');
    }

    public function statutsList(): ResponseInterface
    {
        $rows = db_connect()
            ->table('statut_reservation')
            ->where('deleted_at', null)
            ->orderBy('id_statut_reservation', 'asc')
            ->get()
            ->getResultArray();

        return $this->success(['items' => $rows]);
    }

    private function programmeBuilder(): BaseBuilder
    {
        return db_connect()
            ->table('programme p')
            ->select([
                'p.id_programme',
                'p.id_conducteur',
                'p.id_trajet',
                'p.id_bus',
                'p.date_programme',
                'p.places_disponibles',
                'p.statut',
                'b.numero_plaque',
                'b.marque',
                'b.modele',
                'b.nombre_places',
                'c.nom AS conducteur_nom',
                'c.postnom AS conducteur_postnom',
                'c.prenom AS conducteur_prenom',
                'c.telephone AS conducteur_telephone',
                't.prix',
                't.id_currency',
                't.id_lieu_depart',
                't.id_lieu_arrivee',
                'cur.code_currency',
                'cur.symbole',
                'ld.nom_lieu AS lieu_depart',
                'la.nom_lieu AS lieu_arrivee',
                'h.heure_depart',
                'h.heure_arrivee',
            ])
            ->join('bus b', 'b.id_bus = p.id_bus')
            ->join('conducteur c', 'c.id_conducteur = p.id_conducteur')
            ->join('trajet t', 't.id_trajet = p.id_trajet')
            ->join('currency cur', 'cur.id_currency = t.id_currency')
            ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
            ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
            ->join('horaire h', 'h.id_horaire = t.id_horaire')
            ->where('p.deleted_at', null)
            ->where('b.deleted_at', null)
            ->where('c.deleted_at', null)
            ->where('t.deleted_at', null);
    }

    private function reservationBuilder(): BaseBuilder
    {
        return db_connect()
            ->table('reservation r')
            ->select([
                'r.*',
                'statut_reservation.libelle AS statut_reservation',
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
                'ld.nom_lieu AS lieu_depart',
                'la.nom_lieu AS lieu_arrivee',
                'lr.nom_lieu AS lieu_reservation',
                'h.heure_depart',
                'h.heure_arrivee',
                'cur.code_currency',
                'cur.symbole',
                'mp.libelle AS mode_paiement',
                'pa.reference_paiement',
                'pa.statut_paiement',
                'pa.date_paiement',
                'u.username AS created_by_username',
                'u.nom AS created_by_nom',
                'u.prenom AS created_by_prenom',
            ])
            ->join('statut_reservation', 'statut_reservation.id_statut_reservation = r.id_statut_reservation')
            ->join('client cl', 'cl.id_client = r.id_client')
            ->join('programme p', 'p.id_programme = r.id_programme')
            ->join('bus b', 'b.id_bus = p.id_bus')
            ->join('conducteur c', 'c.id_conducteur = p.id_conducteur')
            ->join('trajet t', 't.id_trajet = p.id_trajet')
            ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart')
            ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee')
            ->join('lieu lr', 'lr.id_lieu = r.id_lieu_reservation', 'left')
            ->join('horaire h', 'h.id_horaire = t.id_horaire')
            ->join('currency cur', 'cur.id_currency = r.id_currency')
            ->join('paiement pa', 'pa.id_reservation = r.id_reservation AND pa.deleted_at IS NULL', 'left')
            ->join('mode_paiement mp', 'mp.id_mode_paiement = pa.id_mode_paiement', 'left')
            ->join('utilisateur u', 'u.id_utilisateur = r.created_by', 'left')
            ->where('r.deleted_at', null);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findProgramme(int $id): ?array
    {
        $row = $this->programmeBuilder()
            ->where('p.id_programme', $id)
            ->get()
            ->getRowArray();

        return is_array($row) ? $row : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findModePaiement(int $id): ?array
    {
        $row = db_connect()
            ->table('mode_paiement')
            ->where('id_mode_paiement', $id)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        return is_array($row) ? $row : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findClientByPhone(string $telephone): ?array
    {
        $row = db_connect()
            ->table('client')
            ->where('telephone', $telephone)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        return is_array($row) ? $row : null;
    }

    /**
     * @param array<string, mixed> $clientPayload
     *
     * @return array<string, mixed>
     */
    private function ensureClient(array $clientPayload): array
    {
        $client = $this->findClientByPhone((string) $clientPayload['telephone']);

        if ($client !== null) {
            return $client;
        }

        $db = db_connect();
        $db->table('client')->insert([
            'telephone' => $clientPayload['telephone'],
            'nom' => $clientPayload['nom'],
            'email' => $clientPayload['email'] ?? null,
        ]);

        return [
            'id_client' => (int) $db->insertID(),
            'telephone' => $clientPayload['telephone'],
            'nom' => $clientPayload['nom'],
            'email' => $clientPayload['email'] ?? null,
        ];
    }

    private function validatedLieuReservation(array $payload, array $programme): int|false|null
    {
        $idLieu = (int) ($payload['id_lieu_reservation'] ?? 0);

        if ($idLieu <= 0) {
            return null;
        }

        $exists = db_connect()
            ->table('arret')
            ->where('id_trajet', (int) $programme['id_trajet'])
            ->where('id_lieu', $idLieu)
            ->where('deleted_at', null)
            ->countAllResults() > 0;

        return $exists ? $idLieu : false;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function reservationDetail(int $id): ?array
    {
        $row = $this->reservationBuilder()
            ->where('r.id_reservation', $id)
            ->get()
            ->getRowArray();

        return is_array($row) ? $row : null;
    }

    private function statusId(string $libelle): ?int
    {
        $row = db_connect()
            ->table('statut_reservation')
            ->select('id_statut_reservation')
            ->where('libelle', $libelle)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        return is_array($row) ? (int) $row['id_statut_reservation'] : null;
    }

    private function firstStatusId(): int
    {
        $row = db_connect()
            ->table('statut_reservation')
            ->select('id_statut_reservation')
            ->where('deleted_at', null)
            ->orderBy('id_statut_reservation', 'asc')
            ->get()
            ->getRowArray();

        return is_array($row) ? (int) $row['id_statut_reservation'] : 1;
    }

    private function currentUserId(): ?int
    {
        $user = service('authContext')->user();

        if (! is_array($user) || empty($user['id_utilisateur'])) {
            return null;
        }

        return (int) $user['id_utilisateur'];
    }

    private function programmeReservable(array $programme): bool
    {
        if ((string) $programme['date_programme'] < date('Y-m-d')) {
            return false;
        }

        $statut = strtolower((string) ($programme['statut'] ?? ''));

        return ! in_array($statut, ['annule', 'annulee', 'termine', 'terminee', 'en cours'], true);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, string>
     */
    private function validateCreatePayload(array $payload): array
    {
        $validation = service('validation');
        $validation->reset();
        $validation->setRules([
            'id_programme' => 'required|is_natural_no_zero',
            'nombre_places' => 'required|is_natural_no_zero',
            'id_mode_paiement' => 'required|is_natural_no_zero',
        ]);

        $errors = $validation->run($payload) ? [] : $validation->getErrors();
        $client = $this->clientPayload($payload);

        if (!empty($payload['id_client'])) {
            return $errors;
        }

        if ($client['telephone'] === '') {
            $errors['telephone'] = 'Le telephone du client est obligatoire.';
        }

        if ($this->findClientByPhone((string) $client['telephone']) === null && $client['nom'] === '') {
            $errors['nom'] = 'Le nom du client est obligatoire si le telephone est nouveau.';
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array{telephone: string, nom: string, email: string|null}
     */
    private function clientPayload(array $payload): array
    {
        $client = is_array($payload['client'] ?? null) ? $payload['client'] : [];

        return [
            'telephone' => $this->normalizePhone((string) ($client['telephone'] ?? $payload['telephone'] ?? '')),
            'nom' => trim((string) ($client['nom'] ?? $payload['nom'] ?? '')),
            'email' => trim((string) ($client['email'] ?? $payload['email'] ?? '')) ?: null,
        ];
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\s+/', '', trim($phone)) ?? '';
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

        $rawInput = $this->request->getRawInput();

        return $rawInput !== [] ? $rawInput : $this->request->getPost();
    }

    private function isDate(string $date): bool
    {
        $value = DateTimeImmutable::createFromFormat('Y-m-d', $date);

        return $value !== false && $value->format('Y-m-d') === $date;
    }

    private function invalidDateResponse(): ResponseInterface
    {
        return $this->failure('Date invalide.', ResponseInterface::HTTP_BAD_REQUEST, [
            'date' => 'La date doit etre au format YYYY-MM-DD.',
        ]);
    }

    private function databaseFailure(Throwable $e): ResponseInterface
    {
        $message = $e->getMessage();

        if (str_contains($message, 'ERR_PLACES_INSUFFISANTES')) {
            return $this->failure('Places insuffisantes.', ResponseInterface::HTTP_CONFLICT);
        }

        if (str_contains($message, 'ERR_PROGRAMME_NON_RESERVABLE')) {
            return $this->failure('Ce programme ne peut pas recevoir de reservation.', ResponseInterface::HTTP_CONFLICT);
        }

        if (str_contains($message, 'Duplicate entry')) {
            return $this->failure('Reference deja utilisee.', ResponseInterface::HTTP_CONFLICT);
        }

        log_message('error', '[ReservationController] ' . $message);

        return $this->failure('Erreur base de donnees.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }
}
