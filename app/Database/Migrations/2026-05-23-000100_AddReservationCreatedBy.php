<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReservationCreatedBy extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('created_by', 'reservation')) {
            $this->db->query('ALTER TABLE `reservation` ADD `created_by` BIGINT NULL AFTER `id_client`');
        }

        if (! $this->indexExists('reservation', 'fk_reservation_created_by')) {
            $this->db->query('ALTER TABLE `reservation` ADD KEY `fk_reservation_created_by` (`created_by`)');
        }

        if (! $this->constraintExists('reservation', 'fk_reservation_created_by')) {
            $this->db->query(
                'ALTER TABLE `reservation` ADD CONSTRAINT `fk_reservation_created_by` '
                . 'FOREIGN KEY (`created_by`) REFERENCES `utilisateur` (`id_utilisateur`)'
            );
        }
    }

    public function down(): void
    {
        if ($this->constraintExists('reservation', 'fk_reservation_created_by')) {
            $this->db->query('ALTER TABLE `reservation` DROP FOREIGN KEY `fk_reservation_created_by`');
        }

        if ($this->indexExists('reservation', 'fk_reservation_created_by')) {
            $this->db->query('ALTER TABLE `reservation` DROP INDEX `fk_reservation_created_by`');
        }

        if ($this->db->fieldExists('created_by', 'reservation')) {
            $this->db->query('ALTER TABLE `reservation` DROP COLUMN `created_by`');
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $row = $this->db->query(
            'SELECT 1 FROM information_schema.STATISTICS '
            . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
            [$table, $index]
        )->getRowArray();

        return is_array($row);
    }

    private function constraintExists(string $table, string $constraint): bool
    {
        $row = $this->db->query(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS '
            . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? LIMIT 1',
            [$table, $constraint]
        )->getRowArray();

        return is_array($row);
    }
}
