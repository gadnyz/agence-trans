<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKashalaTransTriggers extends Migration
{
    public function up(): void
    {
        foreach ($this->triggerNames() as $trigger) {
            $this->db->query(sprintf('DROP TRIGGER IF EXISTS `%s`', $trigger));
        }

        foreach ($this->triggers() as $sql) {
            $this->db->query($sql);
        }
    }

    public function down(): void
    {
        foreach ($this->triggerNames() as $trigger) {
            $this->db->query(sprintf('DROP TRIGGER IF EXISTS `%s`', $trigger));
        }
    }

    /**
     * @return list<string>
     */
    private function triggerNames(): array
    {
        return [
            'tg_check_payment_amount',
            'tg_generate_reference_payment',
            'tg_update_reservation_status_paid',
            'tg_init_places_programme',
            'tg_prevent_bus_double_program',
            'tg_prevent_driver_double_program',
            'tg_calcul_montant_reservation',
            'tg_check_places_before_reservation',
            'tg_generate_reference_reservation',
            'tg_lock_completed_program_reservations',
            'tg_reservation_reduce_places',
            'tg_reservation_update_places',
            'tg_restore_places_after_delete',
            'tg_restore_places_on_status_cancelled',
            'tg_trajet_prix_historique',
            'tg_prevent_bus_double_program_update',
            'tg_prevent_driver_double_program_update',
        ];
    }

    /**
     * @return list<string>
     */
    private function triggers(): array
    {
        return [
            <<<'SQL'
CREATE TRIGGER `tg_check_payment_amount`
BEFORE INSERT ON `paiement`
FOR EACH ROW
BEGIN
  DECLARE total_already_paid DECIMAL(18,2) DEFAULT 0;
  DECLARE reservation_total DECIMAL(18,2) DEFAULT 0;
  DECLARE new_amount_converted DECIMAL(18,2) DEFAULT 0;

  IF COALESCE(NEW.statut_paiement, 'Valide') = 'Valide' THEN
    SELECT COALESCE(montant_final, 0)
    INTO reservation_total
    FROM reservation
    WHERE id_reservation = NEW.id_reservation
      AND deleted_at IS NULL;

    SELECT COALESCE(SUM(COALESCE(montant_paye, 0) / COALESCE(NULLIF(taux_conversion, 0), 1)), 0)
    INTO total_already_paid
    FROM paiement
    WHERE id_reservation = NEW.id_reservation
      AND deleted_at IS NULL
      AND COALESCE(statut_paiement, 'Valide') = 'Valide';

    SET new_amount_converted = COALESCE(NEW.montant_paye, 0) / COALESCE(NULLIF(NEW.taux_conversion, 0), 1);

    IF (total_already_paid + new_amount_converted) > reservation_total THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'ERR_PAIEMENT_DEPASSE_SOLDE';
    END IF;
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_generate_reference_payment`
BEFORE INSERT ON `paiement`
FOR EACH ROW
BEGIN
  IF NEW.reference_paiement IS NULL OR NEW.reference_paiement = '' THEN
    SET NEW.reference_paiement = CONCAT('PAY-', YEAR(NOW()), '-', UUID_SHORT());
  END IF;

  IF NEW.date_paiement IS NULL THEN
    SET NEW.date_paiement = NOW();
  END IF;

  IF NEW.statut_paiement IS NULL OR NEW.statut_paiement = '' THEN
    SET NEW.statut_paiement = 'Valide';
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_update_reservation_status_paid`
AFTER INSERT ON `paiement`
FOR EACH ROW
BEGIN
  DECLARE total_paid_converted DECIMAL(18,2) DEFAULT 0;
  DECLARE reservation_total DECIMAL(18,2) DEFAULT 0;
  DECLARE paid_status_id BIGINT DEFAULT NULL;

  IF COALESCE(NEW.statut_paiement, 'Valide') = 'Valide' THEN
    SELECT COALESCE(montant_final, 0)
    INTO reservation_total
    FROM reservation
    WHERE id_reservation = NEW.id_reservation
      AND deleted_at IS NULL;

    SELECT COALESCE(SUM(COALESCE(montant_paye, 0) / COALESCE(NULLIF(taux_conversion, 0), 1)), 0)
    INTO total_paid_converted
    FROM paiement
    WHERE id_reservation = NEW.id_reservation
      AND deleted_at IS NULL
      AND COALESCE(statut_paiement, 'Valide') = 'Valide';

    SELECT id_statut_reservation
    INTO paid_status_id
    FROM statut_reservation
    WHERE libelle = 'PAYE'
    LIMIT 1;

    IF paid_status_id IS NOT NULL AND total_paid_converted >= reservation_total THEN
      UPDATE reservation
      SET id_statut_reservation = paid_status_id
      WHERE id_reservation = NEW.id_reservation;
    END IF;
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_init_places_programme`
BEFORE INSERT ON `programme`
FOR EACH ROW
BEGIN
  DECLARE capacite_bus INT DEFAULT 0;

  SELECT COALESCE(nombre_places, 0)
  INTO capacite_bus
  FROM bus
  WHERE id_bus = NEW.id_bus
    AND deleted_at IS NULL;

  SET NEW.places_disponibles = COALESCE(NEW.places_disponibles, capacite_bus);
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_prevent_bus_double_program`
BEFORE INSERT ON `programme`
FOR EACH ROW
BEGIN
  DECLARE total_conflits INT DEFAULT 0;
  DECLARE new_heure_dep TIME;
  DECLARE new_heure_arr TIME;

  SELECT h.heure_depart, h.heure_arrivee
  INTO new_heure_dep, new_heure_arr
  FROM trajet t
  INNER JOIN horaire h ON t.id_horaire = h.id_horaire
  WHERE t.id_trajet = NEW.id_trajet;

  SELECT COUNT(*)
  INTO total_conflits
  FROM programme p
  INNER JOIN trajet t ON p.id_trajet = t.id_trajet
  INNER JOIN horaire h ON t.id_horaire = h.id_horaire
  WHERE p.id_bus = NEW.id_bus
    AND p.date_programme = NEW.date_programme
    AND p.deleted_at IS NULL
    AND NEW.deleted_at IS NULL
    AND new_heure_dep < h.heure_arrivee
    AND new_heure_arr > h.heure_depart;

  IF total_conflits > 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'ERR_BUS_DEJA_PLANIFIE';
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_prevent_driver_double_program`
BEFORE INSERT ON `programme`
FOR EACH ROW
BEGIN
  DECLARE total_conflits INT DEFAULT 0;
  DECLARE new_heure_dep TIME;
  DECLARE new_heure_arr TIME;

  SELECT h.heure_depart, h.heure_arrivee
  INTO new_heure_dep, new_heure_arr
  FROM trajet t
  INNER JOIN horaire h ON t.id_horaire = h.id_horaire
  WHERE t.id_trajet = NEW.id_trajet;

  SELECT COUNT(*)
  INTO total_conflits
  FROM programme p
  INNER JOIN trajet t ON p.id_trajet = t.id_trajet
  INNER JOIN horaire h ON t.id_horaire = h.id_horaire
  WHERE p.id_conducteur = NEW.id_conducteur
    AND p.date_programme = NEW.date_programme
    AND p.deleted_at IS NULL
    AND NEW.deleted_at IS NULL
    AND new_heure_dep < h.heure_arrivee
    AND new_heure_arr > h.heure_depart;

  IF total_conflits > 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'ERR_CONDUCTEUR_DEJA_PLANIFIE';
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_calcul_montant_reservation`
BEFORE INSERT ON `reservation`
FOR EACH ROW
BEGIN
  DECLARE prix_trajet DECIMAL(18,2) DEFAULT 0;
  DECLARE trajet_currency_id BIGINT DEFAULT NULL;
  DECLARE waiting_status_id BIGINT DEFAULT NULL;

  SELECT COALESCE(t.prix, 0), t.id_currency
  INTO prix_trajet, trajet_currency_id
  FROM programme p
  INNER JOIN trajet t ON p.id_trajet = t.id_trajet
  WHERE p.id_programme = NEW.id_programme;

  SELECT id_statut_reservation
  INTO waiting_status_id
  FROM statut_reservation
  WHERE libelle = 'EN_ATTENTE'
  LIMIT 1;

  SET NEW.nombre_places = COALESCE(NEW.nombre_places, 1);
  SET NEW.date_reservation = COALESCE(NEW.date_reservation, NOW());
  SET NEW.id_currency = COALESCE(NEW.id_currency, trajet_currency_id);
  SET NEW.id_statut_reservation = COALESCE(NEW.id_statut_reservation, waiting_status_id);
  SET NEW.montant_initial = prix_trajet * NEW.nombre_places;
  SET NEW.montant_reduction = COALESCE(NEW.montant_reduction, 0);
  SET NEW.montant_final = NEW.montant_initial - NEW.montant_reduction;

  IF NEW.montant_final < 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'ERR_REDUCTION_SUPERIEURE_MONTANT';
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_check_places_before_reservation`
BEFORE INSERT ON `reservation`
FOR EACH ROW
BEGIN
  DECLARE available_places INT DEFAULT 0;

  SELECT COALESCE(places_disponibles, 0)
  INTO available_places
  FROM programme
  WHERE id_programme = NEW.id_programme
    AND deleted_at IS NULL;

  IF available_places < COALESCE(NEW.nombre_places, 1) THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'ERR_PLACES_INSUFFISANTES';
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_generate_reference_reservation`
BEFORE INSERT ON `reservation`
FOR EACH ROW
BEGIN
  IF NEW.reference_reservation IS NULL OR NEW.reference_reservation = '' THEN
    SET NEW.reference_reservation = CONCAT('RES-', YEAR(NOW()), '-', UUID_SHORT());
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_lock_completed_program_reservations`
BEFORE INSERT ON `reservation`
FOR EACH ROW
BEGIN
  DECLARE prog_statut VARCHAR(50);

  SELECT statut
  INTO prog_statut
  FROM programme
  WHERE id_programme = NEW.id_programme;

  IF prog_statut IN ('En cours', 'Termine', 'Annule') THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'ERR_PROGRAMME_NON_RESERVABLE';
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_reservation_reduce_places`
AFTER INSERT ON `reservation`
FOR EACH ROW
BEGIN
  DECLARE status_libelle VARCHAR(100);

  SELECT libelle
  INTO status_libelle
  FROM statut_reservation
  WHERE id_statut_reservation = NEW.id_statut_reservation;

  IF status_libelle <> 'ANNULE' THEN
    UPDATE programme
    SET places_disponibles = COALESCE(places_disponibles, 0) - COALESCE(NEW.nombre_places, 0)
    WHERE id_programme = NEW.id_programme
      AND deleted_at IS NULL
      AND COALESCE(places_disponibles, 0) >= COALESCE(NEW.nombre_places, 0);

    IF ROW_COUNT() = 0 THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'ERR_PLACES_INSUFFISANTES';
    END IF;
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_reservation_update_places`
AFTER UPDATE ON `reservation`
FOR EACH ROW
BEGIN
  DECLARE old_status VARCHAR(100);
  DECLARE new_status VARCHAR(100);
  DECLARE old_active TINYINT DEFAULT 0;
  DECLARE new_active TINYINT DEFAULT 0;
  DECLARE diff_places INT DEFAULT 0;

  SELECT libelle INTO old_status
  FROM statut_reservation
  WHERE id_statut_reservation = OLD.id_statut_reservation;

  SELECT libelle INTO new_status
  FROM statut_reservation
  WHERE id_statut_reservation = NEW.id_statut_reservation;

  SET old_active = IF(OLD.deleted_at IS NULL AND old_status <> 'ANNULE', 1, 0);
  SET new_active = IF(NEW.deleted_at IS NULL AND new_status <> 'ANNULE', 1, 0);

  IF old_active = 1 AND (new_active = 0 OR NOT (OLD.id_programme <=> NEW.id_programme)) THEN
    UPDATE programme
    SET places_disponibles = COALESCE(places_disponibles, 0) + COALESCE(OLD.nombre_places, 0)
    WHERE id_programme = OLD.id_programme;
  END IF;

  IF new_active = 1 AND (old_active = 0 OR NOT (OLD.id_programme <=> NEW.id_programme)) THEN
    UPDATE programme
    SET places_disponibles = COALESCE(places_disponibles, 0) - COALESCE(NEW.nombre_places, 0)
    WHERE id_programme = NEW.id_programme
      AND COALESCE(places_disponibles, 0) >= COALESCE(NEW.nombre_places, 0);

    IF ROW_COUNT() = 0 THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'ERR_PLACES_INSUFFISANTES';
    END IF;
  END IF;

  IF old_active = 1
     AND new_active = 1
     AND OLD.id_programme = NEW.id_programme
     AND NOT (OLD.nombre_places <=> NEW.nombre_places) THEN

    SET diff_places = COALESCE(NEW.nombre_places, 0) - COALESCE(OLD.nombre_places, 0);

    IF diff_places > 0 THEN
      UPDATE programme
      SET places_disponibles = COALESCE(places_disponibles, 0) - diff_places
      WHERE id_programme = NEW.id_programme
        AND COALESCE(places_disponibles, 0) >= diff_places;

      IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERR_PLACES_INSUFFISANTES';
      END IF;
    ELSEIF diff_places < 0 THEN
      UPDATE programme
      SET places_disponibles = COALESCE(places_disponibles, 0) + ABS(diff_places)
      WHERE id_programme = NEW.id_programme;
    END IF;
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_restore_places_after_delete`
AFTER DELETE ON `reservation`
FOR EACH ROW
BEGIN
  DECLARE old_status VARCHAR(100);

  SELECT libelle
  INTO old_status
  FROM statut_reservation
  WHERE id_statut_reservation = OLD.id_statut_reservation;

  IF old_status <> 'ANNULE' THEN
    UPDATE programme
    SET places_disponibles = COALESCE(places_disponibles, 0) + COALESCE(OLD.nombre_places, 0)
    WHERE id_programme = OLD.id_programme;
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_trajet_prix_historique`
AFTER UPDATE ON `trajet`
FOR EACH ROW
BEGIN
  DECLARE audit_user_id BIGINT DEFAULT NULL;

  IF @current_user_id IS NOT NULL
     AND EXISTS (
       SELECT 1
       FROM utilisateur
       WHERE id_utilisateur = @current_user_id
     ) THEN
    SET audit_user_id = @current_user_id;
  END IF;

  IF NOT (OLD.prix <=> NEW.prix)
     OR NOT (OLD.id_currency <=> NEW.id_currency) THEN
    INSERT INTO trajet_prix_historique (
      id_trajet,
      ancien_prix,
      nouveau_prix,
      id_currency,
      date_modification,
      id_utilisateur,
      created_at
    )
    VALUES (
      OLD.id_trajet,
      OLD.prix,
      NEW.prix,
      OLD.id_currency,
      NOW(),
      audit_user_id,
      NOW()
    );
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_prevent_bus_double_program_update`
BEFORE UPDATE ON `programme`
FOR EACH ROW
BEGIN
  DECLARE total_conflits INT DEFAULT 0;
  DECLARE new_heure_dep TIME;
  DECLARE new_heure_arr TIME;

  SELECT h.heure_depart, h.heure_arrivee
  INTO new_heure_dep, new_heure_arr
  FROM trajet t
  INNER JOIN horaire h ON t.id_horaire = h.id_horaire
  WHERE t.id_trajet = NEW.id_trajet;

  SELECT COUNT(*)
  INTO total_conflits
  FROM programme p
  INNER JOIN trajet t ON p.id_trajet = t.id_trajet
  INNER JOIN horaire h ON t.id_horaire = h.id_horaire
  WHERE p.id_programme <> OLD.id_programme
    AND p.id_bus = NEW.id_bus
    AND p.date_programme = NEW.date_programme
    AND p.deleted_at IS NULL
    AND NEW.deleted_at IS NULL
    AND new_heure_dep < h.heure_arrivee
    AND new_heure_arr > h.heure_depart;

  IF total_conflits > 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'ERR_BUS_DEJA_PLANIFIE';
  END IF;
END
SQL,
            <<<'SQL'
CREATE TRIGGER `tg_prevent_driver_double_program_update`
BEFORE UPDATE ON `programme`
FOR EACH ROW
BEGIN
  DECLARE total_conflits INT DEFAULT 0;
  DECLARE new_heure_dep TIME;
  DECLARE new_heure_arr TIME;

  SELECT h.heure_depart, h.heure_arrivee
  INTO new_heure_dep, new_heure_arr
  FROM trajet t
  INNER JOIN horaire h ON t.id_horaire = h.id_horaire
  WHERE t.id_trajet = NEW.id_trajet;

  SELECT COUNT(*)
  INTO total_conflits
  FROM programme p
  INNER JOIN trajet t ON p.id_trajet = t.id_trajet
  INNER JOIN horaire h ON t.id_horaire = h.id_horaire
  WHERE p.id_programme <> OLD.id_programme
    AND p.id_conducteur = NEW.id_conducteur
    AND p.date_programme = NEW.date_programme
    AND p.deleted_at IS NULL
    AND NEW.deleted_at IS NULL
    AND new_heure_dep < h.heure_arrivee
    AND new_heure_arr > h.heure_depart;

  IF total_conflits > 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'ERR_CONDUCTEUR_DEJA_PLANIFIE';
  END IF;
END
SQL,
        ];
    }
}
