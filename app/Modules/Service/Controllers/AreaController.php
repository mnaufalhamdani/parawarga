<?php

namespace Service\Controllers;

use App\Controllers\ServiceController;
use CodeIgniter\API\ResponseTrait;
use Service\Models\AreaModel;

class AreaController extends ServiceController
{
    use ResponseTrait;
    
    public function getAreaManagement()
    {
        $userId = $this->request->getGet('user_id');
        
        $model = $this->db
            ->table('area_user as a')
            ->select('b.id, b.area_id, c.area_name, b.start_date, b.end_date, COALESCE(d.total_unit, 0) total_unit, COALESCE(e.total_user, 0) total_user')
            ->join('area_management b', 'a.area_id = b.area_id', 'LEFT')
            ->join('area c', 'a.area_id = c.id', 'LEFT')
            ->join('(SELECT COUNT(id) total_unit, area_id FROM area_unit WHERE status = 1) d', 'a.area_id = d.area_id', 'LEFT')
            ->join('(SELECT COUNT(id) total_user, area_id FROM area_user WHERE status = 1) e', 'a.area_id = e.area_id', 'LEFT')
            ->where('b.status', 1)
            ->where('a.user_id', $userId)
            ->where('b.start_date <=', date('Y-m-d'))
            ->where('b.end_date >=', date('Y-m-d'))
            ->orderBy('b.id', 'DESC')
            ->get()->getResultArray();
        // var_dump($this->db->getLastQuery());

        if(!empty($model)){
            foreach($model as $key => $value){
                $model[$key]['detail'] = $this->db
                    ->table('area_management_detail as a')
                    ->select('a.management_id, a.user_id, a.keterangan, b.name position_name, c.name')
                    ->join('master_option b', 'a.management_position = b.id', 'LEFT')
                    ->join('user c', 'a.user_id = c.id', 'LEFT')
                    ->where('management_id', $value['id'])
                    ->get()->getResultArray();
            }
            
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Area manajemen tidak ditemukan');
        }
    }

    public function saveArea()
    {
        try{
            if($this->request->getMethod() == 'POST'){
                $model = model(AreaModel::class);
                $request = $this->request->getPost('data');
                $data = (array) json_decode($request);
                
                // $data['csrf_test_name'] = 'b167c6f8316886ffac3fd0e0f8f23974';
                $data['id'] = $model->generateCode();
                // $data = $model->findAll();
                // print_r($data);die;
                
    
                if($model->save($data)){
                    return $this->respondSuccess($data);  
                }else {
                    return $this->failNotFound('Data tidak ditemukan');
                }
            }else {
                return $this->failForbidden('Request bad method');
            }
        }catch (\Exception $e) {
            // throw new \Exception($e->getMessage());
            return $this->failForbidden('Request bad method : ' . $e->getMessage());
        }
    }

    public function getAccountBank()
    {
        $areaId = $this->request->getGet('area_id');

        $model = $this->db
            ->table('area_account_bank as a')
            ->select('a.id, a.account_number, a.account_name, a.bank_code, b.code_name, b.name bank_name')
            ->join("master_bank as b", "a.bank_code = b.code", "LEFT")
            ->where('a.area_id', $areaId)
            ->where('a.status', 1)
            ->orderBy('account_name', 'ASC')
            ->get()->getResultArray();

        if(!empty($model)){
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Akun Bank tidak ditemukan');
        }
    }

    public function getUnit()
    {
        $areaId = $this->request->getGet('area_id');
        $userId = $this->request->getGet('user_id');

        $model = $this->db
            ->table('area_unit as a')
            ->select('a.id, a.name, a.additional_desc, a.latitude, a.longitude, a.empty, b.area_name')
            ->join("area as b", "a.area_id = b.id", "LEFT")
            ->where('a.area_id', $areaId)
            ->where('a.created_by', $userId)
            ->where('a.status', 1)
            ->orderBy('b.area_name', 'ASC')
            ->orderBy('a.name', 'ASC')
            ->get()->getResultArray();

        if(!empty($model)){
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Unit tidak ditemukan');
        }
    }

    public function getInformation()
    {
        $areaId = $this->request->getGet('area_id');
        $userId = $this->request->getGet('user_id');

        $model = $this->db
            ->table('area_information as a')
            ->select('a.id, a.title, a.message, a.expired, a.urgent, b.area_name')
            ->join("area as b", "a.area_id = b.id", "LEFT")
            ->where('a.area_id', $areaId)
            ->where('a.status', 1)
            ->where('expired >=', date('Y-m-d'))
            ->orderBy('a.urgent', 'DESC')
            ->orderBy('a.expired', 'ASC')
            ->get()->getResultArray();

        if(!empty($model)){
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Unit tidak ditemukan');
        }
    }
}
