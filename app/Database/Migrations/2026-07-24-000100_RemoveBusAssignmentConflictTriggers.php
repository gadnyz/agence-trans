<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * V1 métier : ne pas bloquer les doubles affectations bus/conducteur.
 * Les places restent gérées (affichage + compteur), sans contrainte d'exclusivité horaire.
 */
class RemoveBusAssignmentConflictTriggers extends Migration
{
    public function up(): void
    {
        foreach ([
            'tg_prevent_bus_double_program',
            'tg_prevent_bus_double_program_update',
            'tg_prevent_driver_double_program',
            'tg_prevent_driver_double_program_update',
        ] as $trigger) {
            $this->db->query(sprintf('DROP TRIGGER IF EXISTS `%s`', $trigger));
        }
    }

    public function down(): void
    {
        // Les définitions complètes restent dans CreateKashalaTransTriggers
        // (restaurées manuellement si un rollback métier est nécessaire).
    }
}
