<?php

use Service\Controllers\AuthController;
use Service\Controllers\AreaController;

$routes->post('service/login', [AuthController::class, 'login']);

$routes->group('service', ['filter' => 'authFilter'], static function ($routes) {
    $routes->get('getAreaManagement', [AreaController::class, 'getAreaManagement']);
    $routes->post('saveArea', [AreaController::class, 'saveArea']);
});
