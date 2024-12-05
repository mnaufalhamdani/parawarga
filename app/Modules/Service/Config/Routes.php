<?php

use Service\Controllers\AuthController;
use Service\Controllers\AreaController;

$routes->post('service/login', [AuthController::class, 'login']);

$routes->group('service', ['filter' => 'authFilter'], static function ($routes) {
    $routes->get('getAreaManagement', [AreaController::class, 'getAreaManagement']);
    $routes->get('getAccountBank', [AreaController::class, 'getAccountBank']);
    $routes->get('getUnit', [AreaController::class, 'getUnit']);
    $routes->get('getInformation', [AreaController::class, 'getInformation']);
    $routes->post('saveArea', [AreaController::class, 'saveArea']);
});
