<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Ticket') ?></title>
    <style>
        @page {
            size: 80mm auto;
            margin: 4mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            width: 72mm;
            margin: 0 auto;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .center {
            text-align: center;
        }

        .logo {
            display: block;
            width: 28px;
            height: 28px;
            object-fit: contain;
            margin: 0 auto 4px;
        }

        .line {
            border-top: 1px dashed #111;
            margin: 8px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin: 3px 0;
        }

        .label {
            color: #444;
        }

        .strong {
            font-weight: 700;
        }

        .total {
            font-size: 16px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <header class="center">
        <img class="logo" src="<?= esc($configuration['logo_url'] ?? base_url('img/bus.png')) ?>" alt="">
        <h1 style="font-size: 16px; margin: 0;"><?= esc($configuration['nom_agence'] ?? 'KASHALA Trans') ?></h1>
        <?php if (! empty($configuration['telephone'])): ?>
            <div><?= esc($configuration['telephone']) ?></div>
        <?php endif; ?>
        <?php if (! empty($configuration['adresse'])): ?>
            <div><?= esc($configuration['adresse']) ?></div>
        <?php endif; ?>
    </header>

    <div class="line"></div>

    <div class="center strong">TICKET BUS</div>
    <div class="center"><?= esc($reservation['reference_reservation']) ?></div>
    <div class="center">
        <span class="label strong">Trajet : </span>
        <span><?= esc($reservation['lieu_depart']) ?> - <?= esc($reservation['lieu_arrivee']) ?></span>
    </div>

    <div class="line"></div>

    <div class="row">
        <span class="label">Client</span>
        <span class="strong"><?= esc($reservation['client_nom']) ?></span>
    </div>
    <div class="row">
        <span class="label">Date</span>
        <span><?= esc($reservation['date_programme']) ?></span>
    </div>
    <div class="row">
        <span class="label">Heure</span>
        <span><?= esc(substr((string) $reservation['heure_depart'], 0, 5)) ?></span>
    </div>
    <div class="row">
        <span class="label">Arrêt</span>
        <span><?= esc($reservation['lieu_reservation'] ?: $reservation['lieu_arrivee']) ?></span>
    </div>
    <div class="row">
        <span class="label">Bus</span>
        <span><?= esc($reservation['numero_plaque']) ?></span>
    </div>
    <div class="row">
        <span class="label">Places</span>
        <span><?= esc($reservation['nombre_places']) ?></span>
    </div>

    <div class="line"></div>

    <div class="row total">
        <span>Total</span>
        <span><?= esc(number_format((float) $reservation['montant_final'], 2, ',', ' ')) ?> <?= esc($reservation['symbole'] ?: $reservation['code_currency']) ?></span>
    </div>
    <div class="row">
        <span class="label">Paiement</span>
        <span><?= esc($reservation['mode_paiement'] ?? '-') ?></span>
    </div>
    <div class="row">
        <span class="label">Référence</span>
        <span><?= esc($reservation['reference_paiement'] ?? '-') ?></span>
    </div>

    <div class="line"></div>

    <footer class="center">
        <div>Merci et bon voyage.</div>
        <div><?= esc(date('Y-m-d H:i')) ?></div>
    </footer>

    <script>
        window.addEventListener('load', () => {
            window.setTimeout(() => window.print(), 250);
        });
    </script>
</body>
</html>
