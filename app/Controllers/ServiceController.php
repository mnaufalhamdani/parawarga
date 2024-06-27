<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class ServiceController extends Controller
{
    protected $request;
    protected $helpers = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //connect database
        $this->db = \Config\Database::connect();
    }    
}

// Generic response method
// $this->respond($data, 200);

// Generic failure response
// $this->fail($errors, 400);

// Item created response
// $this->respondCreated($data);

// Item successfully deleted
// $this->respondDeleted($data);

// Command executed by no response required
// $this->respondNoContent($message);

// Client isn't authorized
// $this->failUnauthorized($description);

// Forbidden action
// $this->failForbidden($description);

// Resource Not Found
// $this->failNotFound($description);

// Data did not validate
// $this->failValidationError($description);

// Resource already exists
// $this->failResourceExists($description);

// Resource previously deleted
// $this->failResourceGone($description);

// Client made too many requests
// $this->failTooManyRequests($description);