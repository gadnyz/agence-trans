<?php

use CodeIgniter\Boot;
use Config\Paths;

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

require FCPATH . '../app/Config/Paths.php';
$paths = new Paths();
require $paths->systemDirectory . '/Boot.php';

// Run setup steps
$r = new ReflectionClass(Boot::class);
$methods = [
    'definePathConstants',
    'loadConstants',
    'checkMissingExtensions',
    'loadDotEnv',
    'defineEnvironment',
    'loadEnvironmentBootstrap',
    'loadCommonFunctions',
    'loadAutoloader',
    'setExceptionHandler',
    'initializeKint',
    'autoloadHelpers'
];

foreach ($methods as $method) {
    $m = $r->getMethod($method);
    $m->setAccessible(true);
    if ($method === 'definePathConstants' || $method === 'loadDotEnv' || $method === 'loadEnvironmentBootstrap') {
        $m->invoke(null, $paths);
    } else {
        $m->invoke(null);
    }
}

// Now we can use the database!
$db = \Config\Database::connect();

$statuses = ['EN_ATTENTE', 'CONFIRME', 'PAYE', 'ANNULE', 'TERMINE'];
foreach ($statuses as $status) {
    $exists = $db->table('statut_reservation')->where('libelle', $status)->countAllResults();
    if (!$exists) {
        $db->table('statut_reservation')->insert(['libelle' => $status]);
        echo "Inserted status: $status\n";
    } else {
        echo "Status $status already exists\n";
    }
}

echo "--- STATUSES ---\n";
print_r($db->table('statut_reservation')->get()->getResultArray());

echo "--- MODES PAIEMENT ---\n";
print_r($db->table('mode_paiement')->get()->getResultArray());

echo "--- CURRENCY ---\n";
print_r($db->table('currency')->get()->getResultArray());
