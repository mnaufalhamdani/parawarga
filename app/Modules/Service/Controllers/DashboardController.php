<?php

namespace Service\Controllers;

use App\Controllers\ServiceController;
use CodeIgniter\API\ResponseTrait;
use Service\Models\AreaModel;

class DashboardController extends ServiceController
{
    use ResponseTrait;
    
    /**
     * get all data related to the dashboard in the application 
     * @param $user_id from token, $area_array data with coma, $date (Y-m-d)
     * @return custom
     */
    public function getViewDashboard()
    {
        $userId = $this->request->user->user_id;
        $areaArray = $this->request->getGet('area_array');
        $date = $this->request->getGet('date');
        
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
            $model['information'] = $this->db
                ->table('area_user as a')
                ->select("b.id, 
                    b.title, 
                    b.message, 
                    b.expired, 
                    b.urgent, 
                    b.created_by, 
                    d.name created_name, 
                    c.area_name,
                    DATE_FORMAT(b.updated_at, '%d %M %Y %H:%i') updated_at")
                ->join("area_information b", "a.area_id = b.area_id", "LEFT")
                ->join("area c", "a.area_id = c.id", "LEFT")
                ->join("user d", "b.created_by = d.id", "LEFT")
                ->where('a.status', 1)
                ->where('b.status', 1)
                ->whereIn('a.area_id', explode(',', $areaArray))
                ->where('b.expired >=', $date)
                ->orderBy('b.urgent', 'DESC')
                ->orderBy('b.expired', 'ASC')
                ->limit(5)
                ->get()->getResultArray();

            $model['issue'] = $this->db
                ->table('area_user as a')
                ->select("b.id, 
                    b.title, 
                    b.message, 
                    b.additional_location, 
                    b.created_by, 
                    d.name created_name, 
                    c.area_name,
                    CONCAT('".base_url()."', 'public/', e.attachment) attachment,
                    DATE_FORMAT(b.updated_at, '%d %M %Y %H:%i') updated_at")
                ->join("area_issue b", "a.area_id = b.area_id", "LEFT")
                ->join("area c", "a.area_id = c.id", "LEFT")
                ->join("user d", "b.created_by = d.id", "LEFT")
                ->join("area_issue_attachment e", "b.id = e.area_id AND e.status_id = 39", "LEFT")
                ->where('a.status', 1)
                ->where('b.status', 1)
                ->whereIn('a.area_id', explode(',', $areaArray))
                ->orderBy('b.updated_at', 'DESC')
                ->limit(5)
                ->get()->getResultArray();

            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Data Dashboard tidak ditemukan');
        }
    }
}