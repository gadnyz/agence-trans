<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();


// =============================================================================
// 1. ROUTES AUTHENTIFICATION (Public)
// =============================================================================
$routes->group('', ['namespace' => 'App\Controllers\Web'], static function ($routes) {
    $routes->get('/', 'AuthController::index');
    $routes->post('login', 'AuthController::login');
    $routes->get('logout', 'AuthController::logout');
    $routes->post('session/refresh', 'AuthController::refresh');
});

// =============================================================================
// 2. ROUTES WEB — Protégées (filtre auth obligatoire sur tout)
// =============================================================================
$routes->group('', ['namespace' => 'App\Controllers\Web', 'filter' => 'auth'], static function ($routes) {

    // -------------------------------------------------------------------------
    // SUPER ADMIN — Accès total (préfixe: /super-admin/)
    // -------------------------------------------------------------------------
    $routes->group('super-admin', ['filter' => 'role:super_admin'], static function ($routes) {
        $routes->get('dashboard',               'DashboardController::superAdminDashboard');
        $routes->get('reservation',             'ReservationController::index');
        $routes->get('reservation/(:num)/ticket', 'ReservationController::ticket/$1');
        $routes->get('planification',           'PlanningController::index');
        $routes->get('rapports',                'ReportController::index');
        $routes->get('analyse',                 'ReportController::analyse');
        $routes->get('parametres',              'ParametresController::index');
    });

    // -------------------------------------------------------------------------
    // ADMIN — Gestion métier (préfixe: /admin/)
    // -------------------------------------------------------------------------
    $routes->group('admin', ['filter' => 'role:admin,super_admin'], static function ($routes) {
        $routes->get('dashboard',               'DashboardController::adminDashboard');
        $routes->get('reservation',             'ReservationController::index');
        $routes->get('reservation/(:num)/ticket', 'ReservationController::ticket/$1');
        $routes->get('planification',           'PlanningController::index');
        $routes->get('rapports',                'ReportController::index');
        $routes->get('bus',                     'ReferenceController::bus');
        $routes->get('chauffeurs',              'ReferenceController::chauffeurs');
        $routes->get('trajets',                 'ReferenceController::trajets');
    });

    // -------------------------------------------------------------------------
    // RÉCEPTIONNISTE — Guichet uniquement (préfixe: /recept/)
    // -------------------------------------------------------------------------
    $routes->group('recept', ['filter' => 'role:recept,admin,super_admin'], static function ($routes) {
        // Tableau de bord (page d'accueil)
        $routes->get('dashboard',                   'ReceptController::index');
        $routes->get('/',                           'ReceptController::index');

        // Réservations
        $routes->get('reservations',                'ReceptController::reservations');
        $routes->get('reservations/(:num)/ticket',  'ReservationController::ticket/$1');

        // Paiements
        $routes->get('paiements',                   'ReceptController::paiements');

        // Programmes de voyage
        $routes->get('programmes',                  'ReceptController::programmes');
        $routes->get('programmes/(:num)/manifeste', 'ReservationController::manifeste/$1');

        // Flotte & Réseau (lecture seule)
        $routes->get('flotte',                      'ReceptController::flotte');
    });

    // -------------------------------------------------------------------------
    // CHAUFFEUR — Lecture planning (préfixe: /driver/)
    // -------------------------------------------------------------------------
    $routes->group('driver', ['filter' => 'role:driver,admin,super_admin'], static function ($routes) {
        $routes->get('planning',                'DriverController::planning');
    });

});

// =============================================================================
// 3. ROUTES API (Backend JSON — Protégées par JWT et Permissions)
// =============================================================================
$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {

    // Auth API (public)
    $routes->post('auth/login',    'AuthController::login');
    $routes->post('auth/refresh',  'AuthController::refresh');

    // Routes protégées par JWT
    $routes->group('', ['filter' => 'jwt'], static function ($routes) {
        $routes->get('auth/me',         'AuthController::me');
        $routes->post('auth/logout',    'AuthController::logout');

        // --- Planification ---
        $routes->get('planification',          'PlanningController::listProgrammes',     ['filter' => 'permission:planning.read,planning.manage']);
        $routes->post('planification',         'PlanningController::createProgrammes',   ['filter' => 'permission:planning.manage']);
        $routes->put('planification/(:num)',   'PlanningController::updateProgramme/$1', ['filter' => 'permission:planning.manage']);
        $routes->delete('planification/(:num)', 'PlanningController::deleteProgramme/$1', ['filter' => 'permission:planning.manage']);
        $routes->get('planification/calendar', 'PlanningController::calendarProgrammes', ['filter' => 'permission:planning.read,planning.manage']);
        
        // Nouvelle route pour le Guichetier (Recherche des voyages)
        $routes->get('planification/search',   'PlanningController::search',             ['filter' => 'permission:planning.read,planning.manage,reservations.manage']);

        // --- Réservations ---
        $routes->get('reservations',    'ReservationController::list',   ['filter' => 'permission:reservations.manage']);
        $routes->post('reservations',   'ReservationController::create', ['filter' => 'permission:reservations.manage,payments.manage']);
        $routes->get('reservations/(:num)', 'ReservationController::show/$1', ['filter' => 'permission:reservations.manage']);
        $routes->put('reservations/(:num)', 'ReservationController::update/$1', ['filter' => 'permission:reservations.manage']);
        $routes->delete('reservations/(:num)', 'ReservationController::delete/$1', ['filter' => 'permission:reservations.manage']);
        $routes->post('reservations/(:num)/cancel', 'ReservationController::cancel/$1', ['filter' => 'permission:reservations.manage']);
        $routes->post('reservations/(:num)/payment', 'ReservationController::addPayment/$1', ['filter' => 'permission:reservations.manage,payments.manage']);
        $routes->get('reservations/statuts', 'ReservationController::statutsList', ['filter' => 'permission:reservations.manage']);

        // --- NOUVEAU : Clients (Guichetier) ---
        $routes->get('clients',  'ClientController::index',  ['filter' => 'permission:clients.manage,reservations.manage']);
        $routes->post('clients', 'ClientController::create', ['filter' => 'permission:clients.manage,reservations.manage']);

        // --- CRUD Référentiel ---
        $registerCrud = function ($routes, $uri, $resource, $readPerm, $writePerm) {
            $routes->get($uri,           "ReferenceDataController::list/$resource",          ['filter' => "permission:$readPerm"]);
            $routes->get("$uri/(:num)",  "ReferenceDataController::detail/$resource/$1",     ['filter' => "permission:$readPerm"]);
            $routes->post($uri,          "ReferenceDataController::store/$resource",         ['filter' => "permission:$writePerm"]);
            $routes->put("$uri/(:num)",   "ReferenceDataController::modify/$resource/$1",    ['filter' => "permission:$writePerm"]);
            $routes->delete("$uri/(:num)", "ReferenceDataController::remove/$resource/$1",   ['filter' => "permission:$writePerm"]);
        };

        $registerCrud($routes, 'bus',         'bus',         'fleet.manage,routes.read,planning.read',                'fleet.manage');
        $registerCrud($routes, 'trajets',     'trajets',     'routes.read,routes.manage',   'routes.manage');
        // Attention : la ligne clients a été retirée d'ici car on la gère avec le ClientController ci-dessus.
        $registerCrud($routes, 'conducteurs', 'conducteurs', 'fleet.manage,routes.read,planning.read',                'fleet.manage');
        $registerCrud($routes, 'horaires',    'horaires',    'routes.read,routes.manage',   'routes.manage');
        $registerCrud($routes, 'lieux',       'lieux',       'routes.read,routes.manage',   'routes.manage');
        $registerCrud($routes, 'utilisateurs', 'utilisateurs', 'settings.manage',             'settings.manage');
    });
});