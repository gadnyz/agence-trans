<?php

namespace App\Controllers\Web;

class PlanningController extends BaseWebController
{
    public function index()
    {
        $meResponse = $this->authenticatedUser();

        if (! is_array($meResponse)) {
            return $meResponse;
        }

        $today = date('Y-m-d');
        $dateDebut = (string) ($this->request->getGet('date_debut') ?? $today);
        $dateFin = (string) ($this->request->getGet('date_fin') ?? date('Y-m-d', strtotime('+14 days')));
        $lieux = $this->items('lieux', ['per_page' => 200]);
        $horaires = $this->items('horaires', ['per_page' => 200]);

        return view('web/pages/planification', [
            'title' => 'Planification',
            'user' => $meResponse['data'],
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

    private function authenticatedUser()
    {
        if (! session()->get('access_token')) {
            return redirect()->to('/');
        }

        $meResponse = $this->api->get('auth/me');

        if (! $meResponse || ($meResponse['success'] ?? false) === false) {
            session()->destroy();

            return redirect()->to('/')->with('error', 'Session expiree, veuillez vous reconnecter.');
        }

        return $meResponse;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function items(string $resource, array $query = []): array
    {
        $response = $this->api->get($resource, $query);

        return $response['data']['items'] ?? [];
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
