<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthRefreshTokens extends Migration
{
    public function up(): void
    {
        $this->db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS `auth_refresh_tokens` (
  `id_refresh_token` BIGINT NOT NULL AUTO_INCREMENT,
  `id_utilisateur` BIGINT NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `issued_at` DATETIME NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `revoked_at` DATETIME DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_refresh_token`),
  UNIQUE KEY `uq_auth_refresh_token_hash` (`token_hash`),
  KEY `idx_auth_refresh_user_active` (`id_utilisateur`, `revoked_at`, `expires_at`, `deleted_at`),
  CONSTRAINT `fk_auth_refresh_user` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS `auth_refresh_tokens`');
    }
}
