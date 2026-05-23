<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nom',
        'postnom',
        'prenom',
        'username',
        'mot_de_passe',
        'telephone',
        'email',
        'id_role',
        'statut',
        'last_connected_at',
        'deleted_at',
    ];

    /**
     * @return array<string, mixed>|null
     */
    public function findActiveByUsername(string $username): ?array
    {
        return $this->baseAuthQuery()
            ->where('u.username', $username)
            ->get()
            ->getRowArray();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findActiveById(int $id): ?array
    {
        return $this->baseAuthQuery()
            ->where('u.id_utilisateur', $id)
            ->get()
            ->getRowArray();
    }

    public function touchLastConnected(int $id): bool
    {
        return $this->update($id, [
            'last_connected_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function baseAuthQuery(): \CodeIgniter\Database\BaseBuilder
    {
        return $this->db->table($this->table . ' u')
            ->select('u.*, r.code_role, r.libelle AS role_libelle')
            ->join('role r', 'r.id_role = u.id_role')
            ->where('u.deleted_at', null)
            ->where('r.deleted_at', null)
            ->where('u.statut', 'Actif');
    }
}
