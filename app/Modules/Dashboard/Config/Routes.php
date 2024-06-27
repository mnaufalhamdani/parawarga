<?php

use Dashboard\Controllers\DashboardController;

$routes->get('/', [DashboardController::class, 'index']);