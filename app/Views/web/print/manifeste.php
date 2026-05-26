<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Liste passagers') ?></title>
    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            font-family: Arial, sans-serif;
            color: #111;
            font-size: 12px;
        }

        h1, h2 {
            margin: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            border-bottom: 2px solid #111;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .brand {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 14px;
        }

        .box {
            border: 1px solid #bbb;
            padding: 8px;
        }

        .label {
            display: block;
            color: #555;
            font-size: 10px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f1f5f9;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="brand">
            <img class="logo" src="<?= esc($configuration['logo_url'] ?? base_url('img/bus.png')) ?>" alt="">
            <div>
                <h1><?= esc($configuration['nom_agence'] ?? 'KASHALA Trans') ?></h1>
                <?php if (! empty($configuration['telephone'])): ?>
                    <div><?= esc($configuration['telephone']) ?></div>
                <?php endif; ?>
                <?php if (! empty($configuration['adresse'])): ?>
                    <div><?= esc($configuration['adresse']) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div style="text-align: right;">
            <h2>Liste des passagers</h2>
            <div>Programme #<?= esc($programme['id_programme']) ?></div>
            <div>Imprimé le <?= esc(date('Y-m-d H:i')) ?></div>
        </div>
    </header>

    <section class="grid">
        <div class="box">
            <span class="label">Trajet</span>
            <strong><?= esc($programme['lieu_depart']) ?> - <?= esc($programme['lieu_arrivee']) ?></strong>
        </div>
        <div class="box">
            <span class="label">Date et heure</span>
            <strong><?= esc($programme['date_programme']) ?>, <?= esc(substr((string) $programme['heure_depart'], 0, 5)) ?></strong>
        </div>
        <div class="box">
            <span class="label">Bus</span>
            <strong><?= esc($programme['numero_plaque']) ?></strong>
            <div><?= esc(trim(($programme['marque'] ?? '') . ' ' . ($programme['modele'] ?? ''))) ?></div>
        </div>
        <div class="box">
            <span class="label">Chauffeur</span>
            <strong><?= esc(trim(($programme['conducteur_nom'] ?? '') . ' ' . ($programme['conducteur_postnom'] ?? '') . ' ' . ($programme['conducteur_prenom'] ?? ''))) ?></strong>
            <?php if (! empty($programme['conducteur_telephone'])): ?>
                <div><?= esc($programme['conducteur_telephone']) ?></div>
            <?php endif; ?>
        </div>
    </section>

    <table>
        <thead>
            <tr>
                <th style="width: 36px;">#</th>
                <th>Client</th>
                <th>Arrêt</th>
                <th>Places</th>
                <th>Référence</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($reservations === []): ?>
                <tr>
                    <td colspan="5">Aucun passager enregistré pour ce programme.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($reservations as $index => $reservation): ?>
                <tr>
                    <td><?= esc($index + 1) ?></td>
                    <td><?= esc($reservation['client_nom']) ?></td>
                    <td><?= esc($reservation['lieu_reservation'] ?: $reservation['lieu_arrivee']) ?></td>
                    <td><?= esc($reservation['nombre_places']) ?></td>
                    <td><?= esc($reservation['reference_reservation']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        window.addEventListener('load', () => {
            window.setTimeout(() => window.print(), 250);
        });
    </script>
</body>
</html>
