<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Libraries\Templates;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['form', 'date'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //Set Session
        session();

        //Set templates for generate view
        $this->templates = new Templates();

        //connect database
        $this->db = \Config\Database::connect();

        //datatables
        $this->datatables = new Datatables(new Codeigniter4Adapter);
    }
    
    public function checkAuthMenu()
    {
        $this->db = \Config\Database::connect();
        $userId = 1;
        $link = '';
        foreach (current_url(true)->getSegments() as $key => $val){
            if($key == 0){
                $link = $val;
            }else{
                $link .= '/' . $val;
            }
        }

        $authMenu = $this->db->query("
            SELECT 
                a.*, b.urutan, b.name menu_name, b.slug, b.link, c.name, c.role_id, d.role
            FROM 
                auth_menu a
            LEFT JOIN
                master_menu b ON a.menu_id = b.id
            LEFT JOIN
                user c ON a.user_id = c.id
            LEFT JOIN
                user_role d ON c.role_id = d.id
            WHERE
                a.user_id = $userId
                AND b.link = '$link'
                AND b.type_id = 1
                AND a.status = 1
            ORDER BY
                b.parent_id ASC, b.urutan ASC
        ")->getRowArray();

        return $authMenu;
    }
}
