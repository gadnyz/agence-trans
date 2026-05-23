<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKashalaTransTables extends Migration
{
    public function up(): void
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($this->tables() as $sql) {
            $this->db->query($sql);
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(): void
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        foreach ([
            'trajet_prix_historique',
            'paiement',
            'reservation',
            'programme_reduction',
            'reduction',
            'programme',
            'arret',
            'trajet',
            'taux_change',
            'configuration',
            'utilisateur',
            'mode_paiement',
            'client',
            'conducteur',
            'bus',
            'horaire',
            'lieu',
            'statut_reservation',
            'role',
            'currency',
        ] as $table) {
            $this->db->query(sprintf('DROP TABLE IF EXISTS `%s`', $table));
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * @return list<string>
     */
    private function tables(): array
    {
        return [
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `currency` (
  `id_currency` BIGINT NOT NULL AUTO_INCREMENT,
  `code_currency` VARCHAR(10) NOT NULL,
  `nom_currency` VARCHAR(100) DEFAULT NULL,
  `symbole` VARCHAR(10) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_currency`),
  UNIQUE KEY `code_currency` (`code_currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `role` (
  `id_role` BIGINT NOT NULL AUTO_INCREMENT,
  `code_role` VARCHAR(50) NOT NULL,
  `libelle` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `code_role` (`code_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `statut_reservation` (
  `id_statut_reservation` BIGINT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_statut_reservation`),
  UNIQUE KEY `uq_statut_reservation_libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `mode_paiement` (
  `id_mode_paiement` BIGINT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(100) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_mode_paiement`),
  UNIQUE KEY `uq_mode_paiement_libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `lieu` (
  `id_lieu` BIGINT NOT NULL AUTO_INCREMENT,
  `nom_lieu` VARCHAR(150) DEFAULT NULL,
  `province` VARCHAR(100) DEFAULT NULL,
  `pays` VARCHAR(100) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_lieu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `horaire` (
  `id_horaire` BIGINT NOT NULL AUTO_INCREMENT,
  `heure_depart` TIME NOT NULL,
  `heure_arrivee` TIME NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_horaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `client` (
  `id_client` BIGINT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) DEFAULT NULL,
  `telephone` VARCHAR(30) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_client`),
  KEY `idx_client_telephone` (`telephone`),
  KEY `idx_client_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `conducteur` (
  `id_conducteur` BIGINT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) DEFAULT NULL,
  `postnom` VARCHAR(100) DEFAULT NULL,
  `prenom` VARCHAR(100) DEFAULT NULL,
  `telephone` VARCHAR(30) DEFAULT NULL,
  `adresse` TEXT DEFAULT NULL,
  `numero_permis` VARCHAR(100) DEFAULT NULL,
  `date_embauche` DATE DEFAULT NULL,
  `statut` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_conducteur`),
  UNIQUE KEY `uq_conducteur_numero_permis` (`numero_permis`),
  KEY `idx_conducteur_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `bus` (
  `id_bus` BIGINT NOT NULL AUTO_INCREMENT,
  `numero_plaque` VARCHAR(50) NOT NULL,
  `marque` VARCHAR(100) DEFAULT NULL,
  `modele` VARCHAR(100) DEFAULT NULL,
  `nombre_places` INT DEFAULT NULL,
  `couleur` VARCHAR(50) DEFAULT NULL,
  `annee` YEAR DEFAULT NULL,
  `statut` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_bus`),
  UNIQUE KEY `numero_plaque` (`numero_plaque`),
  KEY `idx_bus_statut` (`statut`),
  CONSTRAINT `chk_bus_nombre_places` CHECK (`nombre_places` IS NULL OR `nombre_places` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` BIGINT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) DEFAULT NULL,
  `postnom` VARCHAR(100) DEFAULT NULL,
  `prenom` VARCHAR(100) DEFAULT NULL,
  `username` VARCHAR(100) NOT NULL,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `telephone` VARCHAR(30) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `id_role` BIGINT NOT NULL,
  `statut` VARCHAR(50) DEFAULT NULL,
  `last_connected_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `username` (`username`),
  KEY `fk_utilisateur_role` (`id_role`),
  CONSTRAINT `fk_utilisateur_role` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `configuration` (
  `id_configuration` BIGINT NOT NULL AUTO_INCREMENT,
  `nom_agence` VARCHAR(150) NOT NULL,
  `telephone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `adresse` TEXT DEFAULT NULL,
  `taux_defaut` DECIMAL(18,2) DEFAULT NULL,
  `id_currency_defaut` BIGINT NOT NULL,
  `logo` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_configuration`),
  KEY `fk_config_currency` (`id_currency_defaut`),
  CONSTRAINT `fk_config_currency` FOREIGN KEY (`id_currency_defaut`) REFERENCES `currency` (`id_currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `taux_change` (
  `id_taux` BIGINT NOT NULL AUTO_INCREMENT,
  `id_currency_source` BIGINT NOT NULL,
  `id_currency_destination` BIGINT NOT NULL,
  `taux_conversion` DECIMAL(18,4) NOT NULL,
  `date_effet` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_taux`),
  KEY `fk_taux_source` (`id_currency_source`),
  KEY `fk_taux_dest` (`id_currency_destination`),
  KEY `idx_taux_change_pair_date` (`id_currency_source`, `id_currency_destination`, `date_effet`),
  CONSTRAINT `fk_taux_source` FOREIGN KEY (`id_currency_source`) REFERENCES `currency` (`id_currency`),
  CONSTRAINT `fk_taux_dest` FOREIGN KEY (`id_currency_destination`) REFERENCES `currency` (`id_currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `trajet` (
  `id_trajet` BIGINT NOT NULL AUTO_INCREMENT,
  `id_lieu_depart` BIGINT NOT NULL,
  `id_lieu_arrivee` BIGINT NOT NULL,
  `id_horaire` BIGINT NOT NULL,
  `prix` DECIMAL(18,2) NOT NULL,
  `id_currency` BIGINT NOT NULL,
  `distance_km` DECIMAL(10,2) DEFAULT NULL,
  `duree_estimee` VARCHAR(50) DEFAULT NULL,
  `statut` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_trajet`),
  KEY `fk_trajet_depart` (`id_lieu_depart`),
  KEY `fk_trajet_arrivee` (`id_lieu_arrivee`),
  KEY `fk_trajet_horaire` (`id_horaire`),
  KEY `fk_trajet_currency` (`id_currency`),
  KEY `idx_trajet_statut` (`statut`),
  CONSTRAINT `fk_trajet_depart` FOREIGN KEY (`id_lieu_depart`) REFERENCES `lieu` (`id_lieu`),
  CONSTRAINT `fk_trajet_arrivee` FOREIGN KEY (`id_lieu_arrivee`) REFERENCES `lieu` (`id_lieu`),
  CONSTRAINT `fk_trajet_horaire` FOREIGN KEY (`id_horaire`) REFERENCES `horaire` (`id_horaire`),
  CONSTRAINT `fk_trajet_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id_currency`),
  CONSTRAINT `chk_trajet_prix` CHECK (`prix` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `arret` (
  `id_arret` BIGINT NOT NULL AUTO_INCREMENT,
  `id_trajet` BIGINT NOT NULL,
  `id_lieu` BIGINT NOT NULL,
  `ordre_arret` INT DEFAULT NULL,
  `temps_estime` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_arret`),
  KEY `fk_arret_trajet` (`id_trajet`),
  KEY `fk_arret_lieu` (`id_lieu`),
  CONSTRAINT `fk_arret_trajet` FOREIGN KEY (`id_trajet`) REFERENCES `trajet` (`id_trajet`),
  CONSTRAINT `fk_arret_lieu` FOREIGN KEY (`id_lieu`) REFERENCES `lieu` (`id_lieu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `programme` (
  `id_programme` BIGINT NOT NULL AUTO_INCREMENT,
  `id_conducteur` BIGINT NOT NULL,
  `id_trajet` BIGINT NOT NULL,
  `id_bus` BIGINT NOT NULL,
  `date_programme` DATE DEFAULT NULL,
  `places_disponibles` INT DEFAULT NULL,
  `statut` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_programme`),
  KEY `fk_programme_conducteur` (`id_conducteur`),
  KEY `fk_programme_trajet` (`id_trajet`),
  KEY `fk_programme_bus` (`id_bus`),
  KEY `idx_programme_date_statut` (`date_programme`, `statut`),
  KEY `idx_programme_bus_date` (`id_bus`, `date_programme`, `deleted_at`),
  KEY `idx_programme_conducteur_date` (`id_conducteur`, `date_programme`, `deleted_at`),
  CONSTRAINT `fk_programme_conducteur` FOREIGN KEY (`id_conducteur`) REFERENCES `conducteur` (`id_conducteur`),
  CONSTRAINT `fk_programme_trajet` FOREIGN KEY (`id_trajet`) REFERENCES `trajet` (`id_trajet`),
  CONSTRAINT `fk_programme_bus` FOREIGN KEY (`id_bus`) REFERENCES `bus` (`id_bus`),
  CONSTRAINT `chk_programme_places` CHECK (`places_disponibles` IS NULL OR `places_disponibles` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `reduction` (
  `id_reduction` BIGINT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(150) DEFAULT NULL,
  `type_reduction` VARCHAR(50) DEFAULT NULL,
  `valeur` DECIMAL(18,2) DEFAULT NULL,
  `date_debut` DATE DEFAULT NULL,
  `date_fin` DATE DEFAULT NULL,
  `statut` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_reduction`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `programme_reduction` (
  `id_programme` BIGINT NOT NULL,
  `id_reduction` BIGINT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_programme`, `id_reduction`),
  KEY `fk_prog_red_reduction` (`id_reduction`),
  CONSTRAINT `fk_prog_red_programme` FOREIGN KEY (`id_programme`) REFERENCES `programme` (`id_programme`),
  CONSTRAINT `fk_prog_red_reduction` FOREIGN KEY (`id_reduction`) REFERENCES `reduction` (`id_reduction`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `reservation` (
  `id_reservation` BIGINT NOT NULL AUTO_INCREMENT,
  `id_programme` BIGINT NOT NULL,
  `id_client` BIGINT NOT NULL,
  `created_by` BIGINT DEFAULT NULL,
  `id_statut_reservation` BIGINT NOT NULL,
  `date_reservation` DATETIME DEFAULT NULL,
  `id_lieu_reservation` BIGINT DEFAULT NULL,
  `nombre_places` INT DEFAULT NULL,
  `reference_reservation` VARCHAR(100) DEFAULT NULL,
  `montant_initial` DECIMAL(18,2) DEFAULT NULL,
  `montant_reduction` DECIMAL(18,2) DEFAULT NULL,
  `montant_final` DECIMAL(18,2) DEFAULT NULL,
  `id_currency` BIGINT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_reservation`),
  UNIQUE KEY `uq_reservation_reference` (`reference_reservation`),
  KEY `fk_reservation_programme` (`id_programme`),
  KEY `fk_reservation_client` (`id_client`),
  KEY `fk_reservation_created_by` (`created_by`),
  KEY `fk_reservation_statut` (`id_statut_reservation`),
  KEY `fk_reservation_lieu` (`id_lieu_reservation`),
  KEY `fk_reservation_currency` (`id_currency`),
  KEY `idx_reservation_date` (`date_reservation`),
  KEY `idx_reservation_deleted` (`deleted_at`),
  KEY `idx_reservation_statut_deleted` (`id_statut_reservation`, `deleted_at`),
  KEY `idx_reservation_programme_deleted` (`id_programme`, `deleted_at`),
  CONSTRAINT `fk_reservation_programme` FOREIGN KEY (`id_programme`) REFERENCES `programme` (`id_programme`),
  CONSTRAINT `fk_reservation_client` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`),
  CONSTRAINT `fk_reservation_created_by` FOREIGN KEY (`created_by`) REFERENCES `utilisateur` (`id_utilisateur`),
  CONSTRAINT `fk_reservation_statut` FOREIGN KEY (`id_statut_reservation`) REFERENCES `statut_reservation` (`id_statut_reservation`),
  CONSTRAINT `fk_reservation_lieu` FOREIGN KEY (`id_lieu_reservation`) REFERENCES `lieu` (`id_lieu`),
  CONSTRAINT `fk_reservation_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id_currency`),
  CONSTRAINT `chk_reservation_places` CHECK (`nombre_places` IS NULL OR `nombre_places` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `paiement` (
  `id_paiement` BIGINT NOT NULL AUTO_INCREMENT,
  `id_reservation` BIGINT NOT NULL,
  `id_mode_paiement` BIGINT NOT NULL,
  `montant_paye` DECIMAL(18,2) DEFAULT NULL,
  `id_currency` BIGINT NOT NULL,
  `taux_conversion` DECIMAL(18,4) DEFAULT NULL,
  `reference_paiement` VARCHAR(150) DEFAULT NULL,
  `date_paiement` DATETIME DEFAULT NULL,
  `statut_paiement` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_paiement`),
  UNIQUE KEY `uq_paiement_reference` (`reference_paiement`),
  KEY `fk_paiement_reservation` (`id_reservation`),
  KEY `fk_paiement_mode` (`id_mode_paiement`),
  KEY `fk_paiement_currency` (`id_currency`),
  KEY `idx_paiement_date_statut` (`date_paiement`, `statut_paiement`),
  KEY `idx_paiement_reservation_statut_deleted` (`id_reservation`, `statut_paiement`, `deleted_at`),
  CONSTRAINT `fk_paiement_reservation` FOREIGN KEY (`id_reservation`) REFERENCES `reservation` (`id_reservation`),
  CONSTRAINT `fk_paiement_mode` FOREIGN KEY (`id_mode_paiement`) REFERENCES `mode_paiement` (`id_mode_paiement`),
  CONSTRAINT `fk_paiement_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id_currency`),
  CONSTRAINT `chk_paiement_montant` CHECK (`montant_paye` IS NULL OR `montant_paye` >= 0),
  CONSTRAINT `chk_paiement_taux` CHECK (`taux_conversion` IS NULL OR `taux_conversion` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
            <<<'SQL'
CREATE TABLE IF NOT EXISTS `trajet_prix_historique` (
  `id_historique` BIGINT NOT NULL AUTO_INCREMENT,
  `id_trajet` BIGINT NOT NULL,
  `ancien_prix` DECIMAL(18,2) DEFAULT NULL,
  `nouveau_prix` DECIMAL(18,2) DEFAULT NULL,
  `id_currency` BIGINT NOT NULL,
  `date_modification` DATETIME DEFAULT NULL,
  `id_utilisateur` BIGINT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_historique`),
  KEY `fk_hist_trajet` (`id_trajet`),
  KEY `fk_hist_currency` (`id_currency`),
  KEY `fk_hist_user` (`id_utilisateur`),
  CONSTRAINT `fk_hist_trajet` FOREIGN KEY (`id_trajet`) REFERENCES `trajet` (`id_trajet`),
  CONSTRAINT `fk_hist_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id_currency`),
  CONSTRAINT `fk_hist_user` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        ];
    }
}
