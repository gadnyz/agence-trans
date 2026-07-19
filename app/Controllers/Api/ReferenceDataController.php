<?php

namespace App\Controllers\Api;

use App\Api\BaseApiController;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class ReferenceDataController extends BaseApiController
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $resources = [
        'horaires' => [
            'table' => 'horaire',
            'primaryKey' => 'id_horaire',
            'allowedFields' => ['heure_depart', 'heure_arrivee'],
            'searchable' => ['heure_depart', 'heure_arrivee'],
            'orderBy' => 'heure_depart',
            'rules' => [
                'heure_depart' => 'required|regex_match[/^\d{2}:\d{2}(:\d{2})?$/]',
                'heure_arrivee' => 'required|regex_match[/^\d{2}:\d{2}(:\d{2})?$/]',
            ],
        ],
        'bus' => [
            'table' => 'bus',
            'primaryKey' => 'id_bus',
            'allowedFields' => ['numero_plaque', 'marque', 'modele', 'nombre_places', 'couleur', 'annee', 'statut'],
            'searchable' => ['numero_plaque', 'marque', 'modele', 'couleur', 'statut'],
            'orderBy' => 'numero_plaque',
            'rules' => [
                'numero_plaque' => 'required|max_length[50]',
                'marque' => 'permit_empty|max_length[100]',
                'modele' => 'permit_empty|max_length[100]',
                'nombre_places' => 'permit_empty|is_natural_no_zero',
                'couleur' => 'permit_empty|max_length[50]',
                'annee' => 'permit_empty|integer|greater_than_equal_to[1900]',
                'statut' => 'permit_empty|max_length[50]',
            ],
        ],
        'conducteurs' => [
            'table' => 'conducteur',
            'primaryKey' => 'id_conducteur',
            'allowedFields' => ['nom', 'postnom', 'prenom', 'telephone', 'adresse', 'numero_permis', 'date_embauche', 'statut'],
            'searchable' => ['nom', 'postnom', 'prenom', 'telephone', 'numero_permis', 'statut'],
            'orderBy' => 'nom',
            'rules' => [
                'nom' => 'required|max_length[100]',
                'postnom' => 'permit_empty|max_length[100]',
                'prenom' => 'permit_empty|max_length[100]',
                'telephone' => 'permit_empty|max_length[30]',
                'numero_permis' => 'permit_empty|max_length[100]',
                'date_embauche' => 'permit_empty|valid_date[Y-m-d]',
                'statut' => 'permit_empty|max_length[50]',
            ],
        ],
        'lieux' => [
            'table' => 'lieu',
            'primaryKey' => 'id_lieu',
            'allowedFields' => ['nom_lieu', 'province', 'pays', 'description'],
            'searchable' => ['nom_lieu', 'province', 'pays'],
            'orderBy' => 'nom_lieu',
            'rules' => [
                'nom_lieu' => 'required|max_length[150]',
                'province' => 'permit_empty|max_length[100]',
                'pays' => 'permit_empty|max_length[100]',
            ],
        ],
        'clients' => [
            'table' => 'client',
            'primaryKey' => 'id_client',
            'allowedFields' => ['nom', 'telephone', 'email'],
            'searchable' => ['nom', 'telephone', 'email'],
            'orderBy' => 'nom',
            'rules' => [
                'nom' => 'required|max_length[100]',
                'telephone' => 'permit_empty|max_length[30]',
                'email' => 'permit_empty|valid_email|max_length[150]',
            ],
        ],
        'currencies' => [
            'table' => 'currency',
            'primaryKey' => 'id_currency',
            'allowedFields' => ['code_currency', 'nom_currency', 'symbole'],
            'searchable' => ['code_currency', 'nom_currency', 'symbole'],
            'orderBy' => 'code_currency',
            'rules' => [
                'code_currency' => 'required|max_length[10]',
                'nom_currency' => 'permit_empty|max_length[100]',
                'symbole' => 'permit_empty|max_length[10]',
            ],
        ],
        'modes-paiement' => [
            'table' => 'mode_paiement',
            'primaryKey' => 'id_mode_paiement',
            'allowedFields' => ['libelle', 'description'],
            'searchable' => ['libelle', 'description'],
            'orderBy' => 'libelle',
            'rules' => [
                'libelle' => 'required|max_length[100]',
            ],
        ],
        'statuts-reservation' => [
            'table' => 'statut_reservation',
            'primaryKey' => 'id_statut_reservation',
            'allowedFields' => ['libelle'],
            'searchable' => ['libelle'],
            'orderBy' => 'libelle',
            'rules' => [
                'libelle' => 'required|max_length[100]',
            ],
        ],
        'reductions' => [
            'table' => 'reduction',
            'primaryKey' => 'id_reduction',
            'allowedFields' => ['libelle', 'type_reduction', 'valeur', 'date_debut', 'date_fin', 'statut'],
            'searchable' => ['libelle', 'type_reduction', 'statut'],
            'orderBy' => 'libelle',
            'rules' => [
                'libelle' => 'required|max_length[150]',
                'type_reduction' => 'permit_empty|max_length[50]',
                'valeur' => 'permit_empty|decimal',
                'date_debut' => 'permit_empty|valid_date[Y-m-d]',
                'date_fin' => 'permit_empty|valid_date[Y-m-d]',
                'statut' => 'permit_empty|max_length[50]',
            ],
        ],
        'configurations' => [
            'table' => 'configuration',
            'primaryKey' => 'id_configuration',
            'allowedFields' => ['nom_agence', 'telephone', 'email', 'adresse', 'taux_defaut', 'id_currency_defaut', 'logo'],
            'searchable' => ['nom_agence', 'telephone', 'email'],
            'orderBy' => 'nom_agence',
            'rules' => [
                'nom_agence' => 'required|max_length[150]',
                'telephone' => 'permit_empty|max_length[50]',
                'email' => 'permit_empty|valid_email|max_length[150]',
                'taux_defaut' => 'permit_empty|decimal',
                'id_currency_defaut' => 'required|is_natural_no_zero',
            ],
        ],
        'taux-change' => [
            'table' => 'taux_change',
            'primaryKey' => 'id_taux',
            'allowedFields' => ['id_currency_source', 'id_currency_destination', 'taux_conversion', 'date_effet'],
            'searchable' => [],
            'orderBy' => 'date_effet',
            'rules' => [
                'id_currency_source' => 'required|is_natural_no_zero',
                'id_currency_destination' => 'required|is_natural_no_zero',
                'taux_conversion' => 'required|decimal|greater_than[0]',
                'date_effet' => 'permit_empty|valid_date[Y-m-d H:i:s]',
            ],
        ],
        'trajets' => [
            'table' => 'trajet',
            'primaryKey' => 'id_trajet',
            'allowedFields' => ['id_lieu_depart', 'id_lieu_arrivee', 'id_horaire', 'prix', 'id_currency', 'distance_km', 'duree_estimee', 'statut'],
            'searchable' => ['duree_estimee', 'statut'],
            'orderBy' => 'id_trajet',
            'rules' => [
                'id_lieu_depart' => 'required|is_natural_no_zero',
                'id_lieu_arrivee' => 'required|is_natural_no_zero',
                'id_horaire' => 'required|is_natural_no_zero',
                'prix' => 'required|decimal',
                'id_currency' => 'required|is_natural_no_zero',
                'distance_km' => 'permit_empty|decimal',
                'duree_estimee' => 'permit_empty|max_length[50]',
                'statut' => 'permit_empty|max_length[50]',
            ],
        ],
        'utilisateurs' => [
            'table' => 'utilisateur',
            'primaryKey' => 'id_utilisateur',
            'allowedFields' => ['nom', 'postnom', 'prenom', 'username', 'mot_de_passe', 'telephone', 'email', 'id_role', 'statut'],
            'searchable' => ['nom', 'postnom', 'prenom', 'username', 'telephone', 'email', 'statut'],
            'orderBy' => 'nom',
            'rules' => [
                'nom' => 'required|max_length[100]',
                'postnom' => 'permit_empty|max_length[100]',
                'prenom' => 'permit_empty|max_length[100]',
                'username' => 'required|max_length[100]',
                'mot_de_passe' => 'permit_empty|max_length[255]',
                'telephone' => 'permit_empty|max_length[30]',
                'email' => 'permit_empty|valid_email|max_length[150]',
                'id_role' => 'required|is_natural_no_zero',
                'statut' => 'permit_empty|max_length[50]',
            ],
        ],
    ];

    public function list(string $resource): ResponseInterface
    {
        $config = $this->resourceConfig($resource);

        if ($config === null) {
            return $this->notFound();
        }

        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = max(1, min(100, (int) ($this->request->getGet('per_page') ?? 25)));
        $search = trim((string) ($this->request->getGet('q') ?? ''));
        $sort = $this->sortColumn($config);
        $direction = strtolower((string) ($this->request->getGet('direction') ?? 'asc')) === 'desc' ? 'desc' : 'asc';

        $builder = $this->baseBuilder($config);
        $this->applySearch($builder, $config, $search);

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();
        $rows = $builder
            ->orderBy($sort, $direction)
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        return $this->success([
            'items' => $rows,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    public function detail(string $resource, int $id): ResponseInterface
    {
        $config = $this->resourceConfig($resource);

        if ($config === null) {
            return $this->notFound();
        }

        $row = $this->findRow($config, $id);

        if ($row === null) {
            return $this->notFound();
        }

        return $this->success($row);
    }

    public function store(string $resource): ResponseInterface
    {
        $config = $this->resourceConfig($resource);

        if ($config === null) {
            return $this->notFound();
        }

        $payload = $this->filteredPayload($config);

        if ($payload === []) {
            return $this->failure('Aucune donnee valide recue.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $errors = $this->validatePayload($config, $payload, true);

        if ($errors !== []) {
            return $this->failure('Validation echouee.', ResponseInterface::HTTP_BAD_REQUEST, $errors);
        }

        if ($resource === 'utilisateurs') {
            if (!isset($payload['mot_de_passe']) || trim($payload['mot_de_passe']) === '') {
                return $this->failure('Le mot de passe est requis pour un nouvel utilisateur.', ResponseInterface::HTTP_BAD_REQUEST);
            }
            $payload['mot_de_passe'] = password_hash($payload['mot_de_passe'], PASSWORD_DEFAULT);
        }

        try {
            $db = db_connect();
            $db->table($config['table'])->insert($payload);
            $id = (int) $db->insertID();
        } catch (Throwable $e) {
            return $this->databaseFailure($e);
        }

        return $this->success($this->findRow($config, $id), 'Ressource creee.', ResponseInterface::HTTP_CREATED);
    }

    public function modify(string $resource, int $id): ResponseInterface
    {
        $config = $this->resourceConfig($resource);

        if ($config === null) {
            return $this->notFound();
        }

        if ($this->findRow($config, $id) === null) {
            return $this->notFound();
        }

        $payload = $this->filteredPayload($config);

        if ($payload === []) {
            return $this->failure('Aucune donnee valide recue.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $errors = $this->validatePayload($config, $payload, false);

        if ($errors !== []) {
            return $this->failure('Validation echouee.', ResponseInterface::HTTP_BAD_REQUEST, $errors);
        }

        if ($resource === 'utilisateurs') {
            if (isset($payload['mot_de_passe'])) {
                if (trim($payload['mot_de_passe']) === '') {
                    unset($payload['mot_de_passe']);
                } else {
                    $payload['mot_de_passe'] = password_hash($payload['mot_de_passe'], PASSWORD_DEFAULT);
                }
            }
        }

        try {
            db_connect()
                ->table($config['table'])
                ->where($config['primaryKey'], $id)
                ->update($payload);
        } catch (Throwable $e) {
            return $this->databaseFailure($e);
        }

        return $this->success($this->findRow($config, $id), 'Ressource mise a jour.');
    }

    public function remove(string $resource, int $id): ResponseInterface
    {
        $config = $this->resourceConfig($resource);

        if ($config === null) {
            return $this->notFound();
        }

        if ($this->findRow($config, $id) === null) {
            return $this->notFound();
        }

        try {
            db_connect()
                ->table($config['table'])
                ->where($config['primaryKey'], $id)
                ->update(['deleted_at' => date('Y-m-d H:i:s')]);
        } catch (Throwable $e) {
            return $this->databaseFailure($e);
        }

        return $this->success(null, 'Ressource supprimee.');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resourceConfig(string $resource): ?array
    {
        return $this->resources[$resource] ?? null;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function baseBuilder(array $config): BaseBuilder
    {
        return db_connect()
            ->table($config['table'])
            ->where($config['table'] . '.deleted_at', null);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function findRow(array $config, int $id): ?array
    {
        $row = $this->baseBuilder($config)
            ->where($config['primaryKey'], $id)
            ->get()
            ->getRowArray();

        return is_array($row) ? $row : null;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function applySearch(BaseBuilder $builder, array $config, string $search): void
    {
        if ($search === '' || empty($config['searchable'])) {
            return;
        }

        $builder->groupStart();

        foreach ($config['searchable'] as $index => $field) {
            if ($index === 0) {
                $builder->like($field, $search);
            } else {
                $builder->orLike($field, $search);
            }
        }

        $builder->groupEnd();
    }

    /**
     * @param array<string, mixed> $config
     */
    private function sortColumn(array $config): string
    {
        $sort = (string) ($this->request->getGet('sort') ?? '');
        $allowedSorts = array_merge(
            [$config['primaryKey'], 'created_at', 'updated_at'],
            $config['allowedFields']
        );

        if ($sort !== '' && in_array($sort, $allowedSorts, true)) {
            return $sort;
        }

        return (string) ($config['orderBy'] ?? $config['primaryKey']);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    private function filteredPayload(array $config): array
    {
        $payload = $this->payload();
        $allowed = array_flip($config['allowedFields']);

        return array_intersect_key($payload, $allowed);
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

        if ($rawInput !== []) {
            return $rawInput;
        }

        return $this->request->getPost();
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $payload
     *
     * @return array<string, string>
     */
    private function validatePayload(array $config, array $payload, bool $isCreate): array
    {
        $rules = [];

        foreach ($config['rules'] as $field => $rule) {
            if (! $isCreate && ! array_key_exists($field, $payload)) {
                continue;
            }

            $rules[$field] = $isCreate ? $rule : $this->optionalRule((string) $rule);
        }

        if ($rules === []) {
            return [];
        }

        $validation = service('validation');
        $validation->reset();
        $validation->setRules($rules);

        if ($validation->run($payload)) {
            return [];
        }

        return $validation->getErrors();
    }

    private function optionalRule(string $rule): string
    {
        $parts = array_filter(
            explode('|', $rule),
            static fn (string $part): bool => $part !== 'required'
        );

        array_unshift($parts, 'permit_empty');

        return implode('|', array_unique($parts));
    }

    private function notFound(): ResponseInterface
    {
        return $this->failure('Ressource introuvable.', ResponseInterface::HTTP_NOT_FOUND);
    }

    private function databaseFailure(Throwable $e): ResponseInterface
    {
        $message = $e->getMessage();

        if (str_contains($message, 'Duplicate entry')) {
            return $this->failure('Conflit avec une donnee existante.', ResponseInterface::HTTP_CONFLICT);
        }

        log_message('error', '[ReferenceDataController] ' . $message);

        return $this->failure('Erreur base de donnees.', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }
}
