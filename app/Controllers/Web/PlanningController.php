<?php

namespace App\Controllers\Web;

class PlanningController extends BaseWebController
{
    public function index()
    {
        $user = $this->requireAuthApi();

        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $userRole = $this->userRole();
        $layout = match($userRole) {
            'super_admin' => 'web/layouts/super_admin',
            'admin'       => 'web/layouts/admin',
            default       => 'web/layouts/super_admin',
        };

        $today = date('Y-m-d');
        $dateDebut = (string) ($this->request->getGet('date_debut') ?? $today);
        $dateFin = (string) ($this->request->getGet('date_fin') ?? date('Y-m-d', strtotime('+14 days')));
        $lieux = $this->items('lieux', ['per_page' => 200]);
        $horaires = $this->items('horaires', ['per_page' => 200]);

        return view($userRole === 'admin' ? 'web/admin/planification' : 'web/super_admin/planification', [
            'layout' => $layout,
            'title' => 'Planification',
            'user' => $user,
            'api_token' => session()->get('access_token'),
            'bus' => $this->items('bus', ['per_page' => 100, 'sort' => 'numero_plaque']),
            'conducteurs' => $this->items('conducteurs', ['per_page' => 100, 'sort' => 'nom']),
            'trajets' => $this->items('trajets', ['per_page' => 100, 'sort' => 'id_trajet']),
            'lieux_map' => $this->mapBy($lieux, 'id_lieu', 'nom_lieu'),
            'horaires_map' => $this->horaireMap($horaires),
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function items(string $resource, array $query = []): array
    {
        $resources = [
            'bus' => [
                'table' => 'bus',
                'orderBy' => 'numero_plaque',
            ],
            'conducteurs' => [
                'table' => 'conducteur',
                'orderBy' => 'nom',
            ],
            'trajets' => [
                'table' => 'trajet',
                'orderBy' => 'id_trajet',
            ],
            'lieux' => [
                'table' => 'lieu',
                'orderBy' => 'nom_lieu',
            ],
            'horaires' => [
                'table' => 'horaire',
                'orderBy' => 'heure_depart',
            ],
        ];

        $config = $resources[$resource] ?? null;

        if ($config === null) {
            return [];
        }

        $perPage = max(1, min(200, (int) ($query['per_page'] ?? 100)));
        $sort = (string) ($query['sort'] ?? $config['orderBy']);
        $allowedSorts = array_merge([$config['orderBy']], $this->listColumns($config['table']));

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = $config['orderBy'];
        }

        return \Config\Database::connect()
            ->table($config['table'])
            ->where('deleted_at', null)
            ->orderBy($sort, 'asc')
            ->limit($perPage)
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<string>
     */
    private function listColumns(string $table): array
    {
        return array_map(
            static fn (object $field): string => (string) $field->name,
            \Config\Database::connect()->getFieldData($table)
        );
    }

    /**
     * @param list<array<string, mixed>> $items
     *
     * @return array<int|string, mixed>
     */
    private function mapBy(array $items, string $key, string $value): array
    {
        $map = [];

        foreach ($items as $item) {
            if (isset($item[$key])) {
                $map[$item[$key]] = $item[$value] ?? '';
            }
        }

        return $map;
    }

    /**
     * @param list<array<string, mixed>> $horaires
     *
     * @return array<int|string, string>
     */
    private function horaireMap(array $horaires): array
    {
        $map = [];

        foreach ($horaires as $horaire) {
            if (! isset($horaire['id_horaire'])) {
                continue;
            }

            $map[$horaire['id_horaire']] = substr((string) $horaire['heure_depart'], 0, 5)
                . ' - '
                . substr((string) $horaire['heure_arrivee'], 0, 5);
        }

        return $map;
    }

}
