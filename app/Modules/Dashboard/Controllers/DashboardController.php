<?php

namespace Dashboard\Controllers;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->data = [
            'title' => 'Dashboard',
            'route' => 'dashboard',
            'view' => 'Dashboard',
        ];
    }

    public function index()
    {
        return $this->templates->generateLayout($this->data['view'] . '\index', $this->data);
    }
}
