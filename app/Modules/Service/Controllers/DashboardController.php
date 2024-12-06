<?php

namespace Service\Controllers;

use App\Controllers\ServiceController;
use CodeIgniter\API\ResponseTrait;
use Service\Models\AreaModel;

class DashboardController extends ServiceController
{
    use ResponseTrait;
    
    public function getViewDashboard()
    {
        $userId = $this->request->getGet('user_id');
        
        $model = $this->db
            ->table('area_user as a')
            ->select('a.user_id, COALESCE(COUNT(a.id), 0) total_area, COALESCE(COUNT(c.id), 0) total_unit, COALESCE(COUNT(d.id), 0) total_unit_empty')
            ->join('area_license b', 'a.area_id = b.area_id', 'LEFT')
            ->join('area_unit c', 'b.area_id = c.area_id AND a.user_id = c.created_by', 'LEFT')
            ->join('area_unit d', 'b.area_id = d.area_id AND d.empty = 1', 'LEFT')
            ->where('a.status', 1)
            ->where('b.status', 1)
            ->where('a.user_id', $userId)
            ->get()->getRowArray();
        // var_dump($this->db->getLastQuery());

        if(!empty($model['user_id'])){
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Data Dashboard tidak ditemukan');
        }
    }
}