<?php

use Service\Controllers\AuthController;
use Service\Controllers\AreaController;
use Service\Controllers\DashboardController;

$routes->post('service/login', [AuthController::class, 'login']);

$routes->group('service', ['filter' => 'authFilter'], static function ($routes) {
    $routes->get('area/getAreaManagement', [AreaController::class, 'getAreaManagement']);
    $routes->get('area/getAccountBank', [AreaController::class, 'getAccountBank']);
    $routes->get('area/getAllUnit', [AreaController::class, 'getAllUnit']);
    $routes->get('area/getInformation', [AreaController::class, 'getInformation']);
    $routes->post('area/saveArea', [AreaController::class, 'saveArea']);
});

$routes->group('service', ['filter' => 'authFilter'], static function ($routes) {
    $routes->get('dashboard/getViewDashboard', [DashboardController::class, 'getViewDashboard']);
});
