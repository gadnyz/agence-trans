<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthRevokedAccessTokens extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS `auth_revoked_access_tokens` (
  `id_revoked_access_token` BIGINT NOT NULL AUTO_INCREMENT,
  `id_utilisateur` BIGINT NOT NULL,
  `jti` VARCHAR(128) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `revoked_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_revoked_access_token`),
  UNIQUE KEY `uq_auth_revoked_access_jti` (`jti`),
  KEY `idx_auth_revoked_access_user` (`id_utilisateur`),
  KEY `idx_auth_revoked_access_expires` (`expires_at`),
  CONSTRAINT `fk_auth_revoked_access_user` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS `auth_revoked_access_tokens`');
    }
}
