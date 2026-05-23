<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Routes Web (Front-end)
$routes->get('/', 'Web\AuthController::index');
$routes->post('/login', 'Web\AuthController::login');
$routes->get('/logout', 'Web\AuthController::logout');
$routes->post('/session/refresh', 'Web\AuthController::refresh');
$routes->get('/planification', 'Web\PlanningController::index');
$routes->get('/reservations', 'Web\ReservationController::index');
$routes->get('/rapports', 'Web\ReportController::index');
$routes->get('/reservations/(:num)/ticket', 'Web\ReservationController::ticket/$1');
$routes->get('/programmes/(:num)/manifeste', 'Web\ReservationController::manifeste/$1');

$routes->group('api', static function (RouteCollection $routes): void {
    $routes->post('auth/login', 'Api\AuthController::login');
    $routes->post('auth/refresh', 'Api\AuthController::refresh');

    $routes->group('', ['filter' => 'jwt'], static function (RouteCollection $routes): void {
        $routes->get('auth/me', 'Api\AuthController::me');
        $routes->post('auth/logout', 'Api\AuthController::logout');
        $routes->get('planifications', 'Api\PlanningController::listProgrammes', [
            'filter' => 'permission:planning.read,planning.manage',
        ]);
        $routes->get('planifications/calendar', 'Api\PlanningController::calendarProgrammes', [
            'filter' => 'permission:planning.read,planning.manage',
        ]);
        $routes->get('planifications/(:num)', 'Api\PlanningController::showProgramme/$1', [
            'filter' => 'permission:planning.read,planning.manage',
        ]);
        $routes->post('planifications', 'Api\PlanningController::createProgrammes', [
            'filter' => 'permission:planning.manage',
        ]);
        $routes->put('planifications/(:num)', 'Api\PlanningController::updateProgramme/$1', [
            'filter' => 'permission:planning.manage',
        ]);
        $routes->patch('planifications/(:num)', 'Api\PlanningController::updateProgramme/$1', [
            'filter' => 'permission:planning.manage',
        ]);
        $routes->delete('planifications/(:num)', 'Api\PlanningController::deleteProgramme/$1', [
            'filter' => 'permission:planning.manage',
        ]);
        $routes->get('reservations', 'Api\ReservationController::list', [
            'filter' => 'permission:reservations.manage',
        ]);
        $routes->get('reservations/programmes', 'Api\ReservationController::programmesDisponibles', [
            'filter' => 'permission:reservations.manage',
        ]);
        $routes->get('reservations/programmes/(:num)/arrets', 'Api\ReservationController::arretsProgramme/$1', [
            'filter' => 'permission:reservations.manage',
        ]);
        $routes->get('reservations/client', 'Api\ReservationController::clientByPhone', [
            'filter' => 'permission:reservations.manage',
        ]);
        $routes->post('reservations', 'Api\ReservationController::create', [
            'filter' => 'permission:reservations.manage,payments.manage',
        ]);
        $routes->get('reservations/(:num)', 'Api\ReservationController::show/$1', [
            'filter' => 'permission:reservations.manage',
        ]);

        $registerCrud = static function (
            RouteCollection $routes,
            string $uri,
            string $resource,
            string $readPermission,
            string $writePermission
        ): void {
            $routes->get($uri, 'Api\ReferenceDataController::list/' . $resource, [
                'filter' => 'permission:' . $readPermission,
            ]);
            $routes->get($uri . '/(:num)', 'Api\ReferenceDataController::detail/' . $resource . '/$1', [
                'filter' => 'permission:' . $readPermission,
            ]);
            $routes->post($uri, 'Api\ReferenceDataController::store/' . $resource, [
                'filter' => 'permission:' . $writePermission,
            ]);
            $routes->put($uri . '/(:num)', 'Api\ReferenceDataController::modify/' . $resource . '/$1', [
                'filter' => 'permission:' . $writePermission,
            ]);
            $routes->patch($uri . '/(:num)', 'Api\ReferenceDataController::modify/' . $resource . '/$1', [
                'filter' => 'permission:' . $writePermission,
            ]);
            $routes->delete($uri . '/(:num)', 'Api\ReferenceDataController::remove/' . $resource . '/$1', [
                'filter' => 'permission:' . $writePermission,
            ]);
        };

        $registerCrud($routes, 'horaires', 'horaires', 'routes.read,routes.manage', 'routes.manage');
        $registerCrud($routes, 'lieux', 'lieux', 'routes.read,routes.manage', 'routes.manage');
        $registerCrud($routes, 'bus', 'bus', 'fleet.manage', 'fleet.manage');
        $registerCrud($routes, 'conducteurs', 'conducteurs', 'fleet.manage', 'fleet.manage');
        $registerCrud($routes, 'chauffeurs', 'conducteurs', 'fleet.manage', 'fleet.manage');
        $registerCrud($routes, 'clients', 'clients', 'clients.manage', 'clients.manage');
        $registerCrud($routes, 'currencies', 'currencies', 'settings.manage', 'settings.manage');
        $registerCrud($routes, 'modes-paiement', 'modes-paiement', 'payments.manage', 'payments.manage');
        $registerCrud($routes, 'statuts-reservation', 'statuts-reservation', 'reservations.manage', 'reservations.manage');
        $registerCrud($routes, 'reductions', 'reductions', 'reservations.manage', 'reservations.manage');
        $registerCrud($routes, 'configurations', 'configurations', 'settings.manage', 'settings.manage');
        $registerCrud($routes, 'taux-change', 'taux-change', 'settings.manage', 'settings.manage');
        $registerCrud($routes, 'trajets', 'trajets', 'routes.read,routes.manage', 'routes.manage');
    });
});
