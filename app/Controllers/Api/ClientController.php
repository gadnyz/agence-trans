<?php

namespace App\Controllers\Api;

use App\Api\BaseApiController;
use CodeIgniter\HTTP\ResponseInterface;

class ClientController extends BaseApiController
{
    // Recherche de clients pour l'autocomplétion (GET /api/clients)
    public function index(): ResponseInterface
    {
        $search = trim((string) $this->request->getGet('search'));

        $builder = db_connect()->table('client')
            ->where('deleted_at', null)
            ->orderBy('nom', 'asc');

        // Si le guichetier tape un nom ou un numéro
        if ($search !== '') {
            $builder->groupStart()
                ->like('nom', $search)
                ->orLike('telephone', $search)
                ->groupEnd();
        }

        $items = $builder->limit(10)->get()->getResultArray();

        return $this->success([
            'items' => $items,
        ]);
    }

    // Création rapide d'un client depuis le modal (POST /api/clients)
    public function create(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? [];

        $nom = trim((string) ($json['nom'] ?? ''));
        // Nettoyer le numéro de téléphone (enlever les espaces)
        $telephone = preg_replace('/\s+/', '', trim((string) ($json['telephone'] ?? '')));

        if ($nom === '' || $telephone === '') {
            return $this->failure('Le nom et le téléphone sont obligatoires.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $db = db_connect();

        // Vérifier si le numéro de téléphone existe déjà pour éviter les doublons
        $existing = $db->table('client')
            ->where('telephone', $telephone)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if ($existing !== null) {
            return $this->failure('Un client existe déjà avec ce numéro de téléphone.', ResponseInterface::HTTP_CONFLICT);
        }

        // Insertion du nouveau client
        $db->table('client')->insert([
            'nom'        => $nom,
            'telephone'  => $telephone,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $idClient = $db->insertID();

        return $this->success([
            'id_client' => $idClient,
            'nom'       => $nom,
            'telephone' => $telephone
        ], 'Client créé avec succès.', ResponseInterface::HTTP_CREATED);
    }
}