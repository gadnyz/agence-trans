<?php

namespace App\Controllers\Api;

use App\Api\BaseApiController;
use CodeIgniter\HTTP\ResponseInterface;

class PaymentController extends BaseApiController
{
    public function list(): ResponseInterface
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = max(1, min(100, (int) ($this->request->getGet('per_page') ?? 20)));
        $dateDebut = trim((string) ($this->request->getGet('date_debut') ?? ''));
        $dateFin = trim((string) ($this->request->getGet('date_fin') ?? ''));
        $search = trim((string) ($this->request->getGet('search') ?? ''));
        $idMode = (int) ($this->request->getGet('id_mode_paiement') ?? 0);

        $builder = db_connect()
            ->table('paiement pa')
            ->select([
                'pa.id_paiement',
                'pa.reference_paiement',
                'pa.date_paiement',
                'pa.montant_paye',
                'pa.statut_paiement',
                'mp.libelle AS mode_paiement',
                'c.code_currency',
                'c.symbole',
                'r.reference_reservation',
                'r.montant_final',
                'cl.nom AS client_nom',
                'cl.telephone AS client_telephone',
                "CONCAT(COALESCE(ld.nom_lieu, ''), ' → ', COALESCE(la.nom_lieu, '')) AS trajet",
            ])
            ->join('mode_paiement mp', 'mp.id_mode_paiement = pa.id_mode_paiement', 'left')
            ->join('currency c', 'c.id_currency = pa.id_currency', 'left')
            ->join('reservation r', 'r.id_reservation = pa.id_reservation')
            ->join('client cl', 'cl.id_client = r.id_client', 'left')
            ->join('programme p', 'p.id_programme = r.id_programme', 'left')
            ->join('trajet t', 't.id_trajet = p.id_trajet', 'left')
            ->join('lieu ld', 'ld.id_lieu = t.id_lieu_depart', 'left')
            ->join('lieu la', 'la.id_lieu = t.id_lieu_arrivee', 'left')
            ->where('pa.deleted_at', null)
            ->where('r.deleted_at', null);

        if ($dateDebut !== '') {
            $builder->where('DATE(pa.date_paiement) >=', $dateDebut);
        }

        if ($dateFin !== '') {
            $builder->where('DATE(pa.date_paiement) <=', $dateFin);
        }

        if ($idMode > 0) {
            $builder->where('pa.id_mode_paiement', $idMode);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('pa.reference_paiement', $search)
                ->orLike('r.reference_reservation', $search)
                ->orLike('cl.nom', $search)
                ->orLike('cl.telephone', $search)
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $items = $builder
            ->orderBy('pa.date_paiement', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        foreach ($items as &$item) {
            $paid = (float) ($item['montant_paye'] ?? 0);
            $due = (float) ($item['montant_final'] ?? 0);
            $item['restant_du'] = max(0, $due - $paid);
        }
        unset($item);

        return $this->success([
            'items' => $items,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) max(1, ceil($total / $perPage)),
            ],
        ]);
    }
}
