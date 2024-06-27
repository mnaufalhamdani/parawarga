<?php

namespace App\Libraries;
use \Config\Services;

class Templates 
{
    protected $db;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function generateLayout($view = NULL, $data = NULL) 
    {
        $userId = 1;
        $data['menu'] = $this->db->query("
            SELECT 
                * 
            FROM 
                auth_menu a
            LEFT JOIN
                master_menu b ON A.menu_id = b.id
            WHERE
                a.user_id = $userId
                AND b.type_id = 1
                AND a.status = 1
            ORDER BY
                b.parent_id ASC, b.urutan ASC
        ")->getResultArray();
        $data['menu'] = $this->getActivePage($data['menu']);
        $data['breadcrumbs'] = $this->generateBreadcrumbs();
        
        return view($view, $data);
    }

    public function getActivePage($menu = null)
    {
        $pattern =  array('/-/', '/_/');
        foreach ($menu as $key => $val){
            $menu[$key]['toggle'] = '';
            $menu[$key]['is_active'] = '';

            if($val['link'] == '#'){
                $menu[$key]['toggle'] = 'menu-toggle';
                $menu[$key]['link'] = '';
            }

            if(current_url(true)->getTotalSegments() == 0){//for dashboard
                if(strtolower($val['name']) == 'dashboard'){
                    $menu[$key]['is_active'] = 'active';
                }
            }

            foreach (current_url(true)->getSegments() as $keyChild => $valChild){
                if(strtolower($val['name']) == strtolower(preg_replace($pattern, ' ', $valChild))){
                    $menu[$key]['is_active'] = 'active';
                }
            }
        }

        return $menu;
    }
    
    public function generateBreadcrumbs()
    {
        $router = Services::routes();
        $services = (array) $router->getRoutes();
        $breadcrumbs[] = ['name' => 'Dashboard', 'url' => base_url(), 'is_enabled' => 'enabled'];
        $tempBreadcrumbs = '';
        $pattern =  array('/-/', '/_/');
        foreach (current_url(true)->getSegments() as $key => $val){
            if($key > 0){
                $tempBreadcrumbs .= "/$val";
            }else{
                $tempBreadcrumbs = $val;
            }
            
            foreach ($services as $keyChild => $valChild){
                if(str_replace('/(.*)', '', $keyChild) == $tempBreadcrumbs){
                    $breadcrumbs[] = ['name' => "/", 'url' => '', 'is_enabled' => 'disabled'];
                    $breadcrumbs[] = ['name' => ucwords(preg_replace($pattern, ' ', $val)), 'url' => base_url($keyChild), 'is_enabled' => 'enabled'];
                }
            }
        }

        $breadcrumbs[count($breadcrumbs) - 1]['is_enabled'] = 'disabled';
        return $breadcrumbs;
    }
}
?>