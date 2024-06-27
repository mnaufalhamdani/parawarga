<?php

use Master\Controllers\KelurahanController;
use Master\Controllers\MenuController;
use Master\Controllers\AreaController;
use Master\Controllers\OptionTypeController;
use Master\Controllers\OptionController;

$routes->group('master', static function ($routes) {
    $routes->get('data_kelurahan', [KelurahanController::class, 'get_data']);
    $routes->get('kelurahan', [KelurahanController::class, 'index']);
    
    $routes->get('data_menu', [MenuController::class, 'get_data']);
    $routes->get('menu', [MenuController::class, 'index']);
    $routes->get('menu/create', [MenuController::class, 'create']);
    $routes->post('menu/onCreate', [MenuController::class, 'onCreate']);
    $routes->get('menu/update/(:any)', [MenuController::class, 'update']);
    $routes->post('menu/onUpdate', [MenuController::class, 'onUpdate']);
    $routes->delete('menu/onDelete', [MenuController::class, 'onDelete']);
    $routes->get('menu/detail/(:any)', [MenuController::class, 'detail']);
    
    $routes->get('data_area', [AreaController::class, 'get_data']);
    $routes->post('area/get_data_kabupaten', [AreaController::class, 'get_data_kabupaten']);
    $routes->post('area/get_data_kecamatan', [AreaController::class, 'get_data_kecamatan']);
    $routes->post('area/get_data_kelurahan', [AreaController::class, 'get_data_kelurahan']);
    $routes->get('area', [AreaController::class, 'index']);
    $routes->get('area/create', [AreaController::class, 'create']);
    $routes->post('area/onCreate', [AreaController::class, 'onCreate']);
    $routes->get('area/update/(:any)', [AreaController::class, 'update']);
    $routes->post('area/onUpdate', [AreaController::class, 'onUpdate']);
    $routes->delete('area/onDelete', [AreaController::class, 'onDelete']);
    $routes->get('area/detail/(:any)', [AreaController::class, 'detail']);

    $routes->get('data_option_type', [OptionTypeController::class, 'get_data']);
    $routes->get('option-type', [OptionTypeController::class, 'index']);
    $routes->get('option-type/create', [OptionTypeController::class, 'create']);
    $routes->post('option-type/onCreate', [OptionTypeController::class, 'onCreate']);
    $routes->get('option-type/update/(:any)', [OptionTypeController::class, 'update']);
    $routes->post('option-type/onUpdate', [OptionTypeController::class, 'onUpdate']);
    $routes->delete('option-type/onDelete', [OptionTypeController::class, 'onDelete']);
    $routes->get('option-type/detail/(:any)', [OptionTypeController::class, 'detail']);

    $routes->get('data_option', [OptionController::class, 'get_data']);
    $routes->get('option', [OptionController::class, 'index']);
    $routes->get('option/create', [OptionController::class, 'create']);
    $routes->post('option/onCreate', [OptionController::class, 'onCreate']);
    $routes->get('option/update/(:any)', [OptionController::class, 'update']);
    $routes->post('option/onUpdate', [OptionController::class, 'onUpdate']);
    $routes->delete('option/onDelete', [OptionController::class, 'onDelete']);
    $routes->get('option/detail/(:any)', [OptionController::class, 'detail']);
});
