<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTrajetsInterval extends Migration
{
    public function up()
    {
        // 1. Ajouter les nouveaux horaires (avec 30 minutes d'écart)
        $horaires = [
            [
                'heure_depart'  => '08:30:00',
                'heure_arrivee' => '11:00:00', // 2h30 de trajet estimé
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ],
            [
                'heure_depart'  => '09:30:00',
                'heure_arrivee' => '12:00:00',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ],
            [
                'heure_depart'  => '15:30:00',
                'heure_arrivee' => '18:00:00',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ],
            [
                'heure_depart'  => '16:30:00',
                'heure_arrivee' => '19:00:00',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]
        ];

        $horaireIds = [];
        foreach ($horaires as $h) {
            $this->db->table('horaire')->insert($h);
            $horaireIds[$h['heure_depart']] = $this->db->insertID();
        }

        // 2. Récupérer les ID des lieux
        // Lubumbashi = 1, Mokambo = 2, Devise par défaut CDF (id_currency = 1)
        // Trajets à ajouter :
        // - Mokambo -> Lubumbashi à 08:30 (Matin)
        // - Lubumbashi -> Mokambo à 09:30 (Matin)
        // - Lubumbashi -> Mokambo à 15:30 (Soir/Après-midi)
        // - Mokambo -> Lubumbashi à 16:30 (Soir/Après-midi)
        
        $trajets = [
            [
                'id_lieu_depart'  => 2, // Mokambo
                'id_lieu_arrivee' => 1, // Lubumbashi
                'id_horaire'      => $horaireIds['08:30:00'],
                'prix'            => 15000.00,
                'id_currency'     => 1,
                'distance_km'     => 120.00,
                'duree_estimee'   => '2h30',
                'statut'          => 'Actif',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s')
            ],
            [
                'id_lieu_depart'  => 1, // Lubumbashi
                'id_lieu_arrivee' => 2, // Mokambo
                'id_horaire'      => $horaireIds['09:30:00'],
                'prix'            => 15000.00,
                'id_currency'     => 1,
                'distance_km'     => 120.00,
                'duree_estimee'   => '2h30',
                'statut'          => 'Actif',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s')
            ],
            [
                'id_lieu_depart'  => 1, // Lubumbashi
                'id_lieu_arrivee' => 2, // Mokambo
                'id_horaire'      => $horaireIds['15:30:00'],
                'prix'            => 15000.00,
                'id_currency'     => 1,
                'distance_km'     => 120.00,
                'duree_estimee'   => '2h30',
                'statut'          => 'Actif',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s')
            ],
            [
                'id_lieu_depart'  => 2, // Mokambo
                'id_lieu_arrivee' => 1, // Lubumbashi
                'id_horaire'      => $horaireIds['16:30:00'],
                'prix'            => 15000.00,
                'id_currency'     => 1,
                'distance_km'     => 120.00,
                'duree_estimee'   => '2h30',
                'statut'          => 'Actif',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s')
            ]
        ];

        // foreach ($trajets as $t) {
        //     $this->db->table('trajet')->insert($t);
        // }
    }

    public function down()
    {
        // 1. Récupérer les ID des horaires ajoutés
        $heures = ['08:30:00', '09:30:00', '15:30:00', '16:30:00'];
        
        $builder = $this->db->table('horaire');
        $builder->whereIn('heure_depart', $heures);
        $query = $builder->get();
        $horaires = $query->getResultArray();
        
        $horaireIds = array_column($horaires, 'id_horaire');

        if (!empty($horaireIds)) {
            // 2. Supprimer les trajets associés
            $this->db->table('trajet')->whereIn('id_horaire', $horaireIds)->delete();
            
            // 3. Supprimer les horaires
            $this->db->table('horaire')->whereIn('id_horaire', $horaireIds)->delete();
        }
    }
}
