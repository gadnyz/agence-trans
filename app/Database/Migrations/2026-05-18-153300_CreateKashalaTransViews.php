<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKashalaTransViews extends Migration
{
    public function up(): void
    {
        foreach ($this->viewNames() as $view) {
            $this->db->query(sprintf('DROP VIEW IF EXISTS `%s`', $view));
        }

        foreach ($this->views() as $sql) {
            $this->db->query($sql);
        }
    }

    public function down(): void
    {
        foreach ($this->viewNames() as $view) {
            $this->db->query(sprintf('DROP VIEW IF EXISTS `%s`', $view));
        }
    }

    /**
     * @return list<string>
     */
    private function viewNames(): array
    {
        return [
            'v_analyse_financiere_trajets',
            'v_analyse_modes_paiement',
            'v_analyse_performance_conducteurs',
            'v_analyse_remplissage_flotte',
            'v_analyse_temporelle_demande',
            'v_detail_programmes',
            'v_paiements_incomplets',
        ];
    }

    /**
     * @return list<string>
     */
    private function views(): array
    {
        return [
            <<<'VIEWSQL'
CREATE OR REPLACE
ALGORITHM = UNDEFINED
SQL SECURITY INVOKER
VIEW `v_analyse_financiere_trajets` AS
SELECT
  t.id_trajet,
  l_dep.nom_lieu AS ville_depart,
  l_arr.nom_lieu AS ville_arrivee,
  CONCAT(l_dep.nom_lieu, ' - ', l_arr.nom_lieu) AS axe_routier,
  c_res.code_currency AS devise_reservation,
  COUNT(DISTINCT r.id_reservation) AS total_reservations_valides,
  COALESCE(SUM(r.nombre_places), 0) AS total_sieges_vendus,
  COALESCE(SUM(r.montant_initial), 0) AS chiffre_affaires_brut,
  COALESCE(SUM(r.montant_reduction), 0) AS total_reductions_accordees,
  COALESCE(SUM(r.montant_final), 0) AS chiffre_affaires_net,
  COALESCE(AVG(r.montant_final), 0) AS panier_moyen_reservation
FROM trajet t
JOIN lieu l_dep ON t.id_lieu_depart = l_dep.id_lieu
JOIN lieu l_arr ON t.id_lieu_arrivee = l_arr.id_lieu
JOIN programme p ON t.id_trajet = p.id_trajet AND p.deleted_at IS NULL
JOIN reservation r ON p.id_programme = r.id_programme AND r.deleted_at IS NULL
JOIN statut_reservation sr ON r.id_statut_reservation = sr.id_statut_reservation
JOIN currency c_res ON r.id_currency = c_res.id_currency
WHERE t.deleted_at IS NULL
  AND sr.libelle <> 'ANNULE'
GROUP BY t.id_trajet, l_dep.nom_lieu, l_arr.nom_lieu, c_res.code_currency
VIEWSQL,
            <<<'VIEWSQL'
CREATE OR REPLACE
ALGORITHM = UNDEFINED
SQL SECURITY INVOKER
VIEW `v_analyse_modes_paiement` AS
SELECT
  mp.libelle AS mode_paiement,
  curr.code_currency AS devise_encaissement,
  COUNT(p.id_paiement) AS volume_transactions,
  COALESCE(SUM(p.montant_paye), 0) AS montant_total_encaisse,
  COALESCE(AVG(p.montant_paye), 0) AS valeur_transaction_moyenne
FROM mode_paiement mp
JOIN paiement p ON mp.id_mode_paiement = p.id_mode_paiement AND p.deleted_at IS NULL
JOIN currency curr ON p.id_currency = curr.id_currency
WHERE mp.deleted_at IS NULL
  AND p.statut_paiement = 'Valide'
GROUP BY mp.libelle, curr.code_currency
VIEWSQL,
            <<<'VIEWSQL'
CREATE OR REPLACE
ALGORITHM = UNDEFINED
SQL SECURITY INVOKER
VIEW `v_analyse_performance_conducteurs` AS
SELECT
  c.id_conducteur,
  TRIM(CONCAT(COALESCE(c.nom, ''), ' ', COALESCE(c.prenom, ''))) AS nom_complet,
  c.telephone,
  COUNT(p.id_programme) AS total_voyages_assures,
  COUNT(DISTINCT p.id_bus) AS nombre_bus_differents_utilises
FROM conducteur c
LEFT JOIN programme p ON c.id_conducteur = p.id_conducteur AND p.deleted_at IS NULL
WHERE c.deleted_at IS NULL
GROUP BY c.id_conducteur, c.nom, c.prenom, c.telephone
VIEWSQL,
            <<<'VIEWSQL'
CREATE OR REPLACE
ALGORITHM = UNDEFINED
SQL SECURITY INVOKER
VIEW `v_analyse_remplissage_flotte` AS
SELECT
  b.id_bus,
  b.numero_plaque,
  b.marque,
  b.modele,
  b.nombre_places AS capacite_maximale,
  COUNT(p.id_programme) AS nombre_voyages_planifies,
  COALESCE(SUM(b.nombre_places - p.places_disponibles), 0) AS total_passagers_transportes,
  COALESCE(AVG((b.nombre_places - p.places_disponibles) / NULLIF(b.nombre_places, 0) * 100), 0) AS taux_remplissage_moyen
FROM bus b
LEFT JOIN programme p ON b.id_bus = p.id_bus AND p.deleted_at IS NULL
WHERE b.deleted_at IS NULL
GROUP BY b.id_bus, b.numero_plaque, b.marque, b.modele, b.nombre_places
VIEWSQL,
            <<<'VIEWSQL'
CREATE OR REPLACE
ALGORITHM = UNDEFINED
SQL SECURITY INVOKER
VIEW `v_analyse_temporelle_demande` AS
SELECT
  YEAR(r.date_reservation) AS annee,
  MONTH(r.date_reservation) AS mois,
  MONTHNAME(r.date_reservation) AS nom_mois,
  WEEKDAY(r.date_reservation) AS jour_semaine_index,
  DAYNAME(r.date_reservation) AS nom_jour,
  COUNT(r.id_reservation) AS nombre_reservations,
  COALESCE(SUM(r.nombre_places), 0) AS sieges_reserves
FROM reservation r
JOIN statut_reservation sr ON r.id_statut_reservation = sr.id_statut_reservation
WHERE r.deleted_at IS NULL
  AND sr.libelle <> 'ANNULE'
GROUP BY
  YEAR(r.date_reservation),
  MONTH(r.date_reservation),
  MONTHNAME(r.date_reservation),
  WEEKDAY(r.date_reservation),
  DAYNAME(r.date_reservation)
VIEWSQL,
            <<<'VIEWSQL'
CREATE OR REPLACE
ALGORITHM = UNDEFINED
SQL SECURITY INVOKER
VIEW `v_detail_programmes` AS
SELECT
  p.id_programme,
  p.date_programme,
  p.statut AS statut_programme,
  p.places_disponibles,
  h.heure_depart,
  h.heure_arrivee,
  CONCAT(l_dep.nom_lieu, ' - ', l_arr.nom_lieu) AS axe_routier,
  t.prix AS prix_base_trajet,
  c_trajet.code_currency AS devise_trajet,
  TRIM(CONCAT(COALESCE(cond.nom, ''), ' ', COALESCE(cond.prenom, ''))) AS nom_conducteur,
  b.numero_plaque AS plaque_bus,
  b.marque AS marque_bus,
  b.nombre_places AS capacite_bus,
  COALESCE(b.nombre_places, 0) - COALESCE(p.places_disponibles, 0) AS passagers_actifs
FROM programme p
JOIN trajet t ON p.id_trajet = t.id_trajet AND t.deleted_at IS NULL
JOIN horaire h ON t.id_horaire = h.id_horaire AND h.deleted_at IS NULL
JOIN lieu l_dep ON t.id_lieu_depart = l_dep.id_lieu
JOIN lieu l_arr ON t.id_lieu_arrivee = l_arr.id_lieu
JOIN currency c_trajet ON t.id_currency = c_trajet.id_currency
JOIN conducteur cond ON p.id_conducteur = cond.id_conducteur AND cond.deleted_at IS NULL
JOIN bus b ON p.id_bus = b.id_bus AND b.deleted_at IS NULL
WHERE p.deleted_at IS NULL
VIEWSQL,
            <<<'VIEWSQL'
CREATE OR REPLACE
ALGORITHM = UNDEFINED
SQL SECURITY INVOKER
VIEW `v_paiements_incomplets` AS
SELECT
  r.id_reservation,
  r.reference_reservation,
  r.date_reservation,
  cl.nom AS nom_client,
  cl.telephone AS telephone_client,
  r.montant_final AS total_du_reservation,
  c.code_currency AS devise_reservation,
  COALESCE(SUM(pa.montant_paye / COALESCE(NULLIF(pa.taux_conversion, 0), 1)), 0) AS total_recouvre,
  r.montant_final - COALESCE(SUM(pa.montant_paye / COALESCE(NULLIF(pa.taux_conversion, 0), 1)), 0) AS reste_a_payer
FROM reservation r
JOIN client cl ON r.id_client = cl.id_client
JOIN currency c ON r.id_currency = c.id_currency
JOIN statut_reservation sr ON r.id_statut_reservation = sr.id_statut_reservation
LEFT JOIN paiement pa
  ON r.id_reservation = pa.id_reservation
  AND pa.deleted_at IS NULL
  AND pa.statut_paiement = 'Valide'
WHERE r.deleted_at IS NULL
  AND sr.libelle NOT IN ('ANNULE', 'TERMINE')
GROUP BY
  r.id_reservation,
  r.reference_reservation,
  r.date_reservation,
  cl.nom,
  cl.telephone,
  r.montant_final,
  c.code_currency
HAVING reste_a_payer > 0.01
VIEWSQL,
        ];
    }
}
