<?php

namespace Service\Controllers;

use App\Controllers\ServiceController;
use CodeIgniter\API\ResponseTrait;
use Service\Models\AreaModel;

class AreaController extends ServiceController
{
    use ResponseTrait;

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

    public function getAreaManagement()
    {
        $areaArray = $this->request->getGet('area_array');
        $date = $this->request->getGet('date');//Y-m-d

        $model = $this->db
            ->table('area_user as a')
            ->select('b.id, 
                b.area_id, 
                c.area_name, 
                b.start_date, 
                b.end_date, 
                COALESCE(COUNT(DISTINCT d.id), 0) total_unit, 
                COALESCE(COUNT(DISTINCT a.id), 0) total_user')
            ->join('area_management b', 'a.area_id = b.area_id', 'LEFT')
            ->join('area c', 'a.area_id = c.id', 'LEFT')
            ->join('area_unit d', 'a.area_id = d.area_id AND d.status = 1', 'LEFT')
            ->where('a.status', 1)
            ->where('b.status', 1)
            ->whereIn('a.area_id', explode(',', $areaArray))
            ->where('b.start_date <=', $date)
            ->where('b.end_date >=', $date)
            ->groupBy('a.area_id')
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

    public function getAllUnit()
    {
        $areaArray = $this->request->getGet('area_array');
        $empty = $this->request->getGet('empty');
        $userId = $this->request->getGet('user_id');
        $andWhere = '';

        if(!empty($empty)){
            $andWhere .= ' and c.empty = ' . $empty;
        }

        if(!empty($userId)){
            $andWhere .= ' and c.created_by = ' . $userId;
        }

        $model = $this->db
            ->table('area_user as a')
            ->select('c.id, 
                c.created_by, 
                d.name created_name,
                c.name, 
                c.additional_desc, 
                c.latitude, 
                c.longitude, 
                c.contract, 
                c.empty, 
                b.area_name')
            ->join("area as b", "a.area_id = b.id", "LEFT")
            ->join('area_unit c', 'a.area_id = c.area_id', 'LEFT')
            ->join('user d', 'c.created_by = d.id', 'LEFT')
            ->where('a.status', 1)
            ->where('c.status', 1)
            ->where((!empty($andWhere)) ? substr($andWhere, 5) : 'a.status = 1')
            ->whereIn('a.area_id', explode(',', $areaArray))
            ->orderBy('b.area_name', 'ASC')
            ->orderBy('c.name', 'ASC')
            ->get()->getResultArray();

        if(!empty($model)){
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Unit tidak ditemukan');
        }
    }

    public function getAccountBank()
    {
        $areaId = $this->request->getGet('area_id');

        $model = $this->db
            ->table('area_account_bank as a')
            ->select('a.id, a.account_number, a.account_name, a.bank_code, c.code_name, c.name bank_name')
            ->join('area_license b', 'a.area_id = b.area_id', 'LEFT')
            ->join("master_bank as c", "a.bank_code = c.code", "LEFT")
            ->where('a.area_id', $areaId)
            ->where('a.status', 1)
            ->where('b.status', 1)
            ->orderBy('a.account_name', 'ASC')
            ->get()->getResultArray();

        if(!empty($model)){
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Akun Bank tidak ditemukan');
        }
    }

    public function getInformation()
    {
        $areaArray = $this->request->getGet('area_array');
        $date = $this->request->getGet('date');//Y-m-d

        $model = $this->db
            ->table('area_user as a')
            ->select('b.id, b.title, b.message, b.expired, b.urgent, b.created_by, d.name created_name, c.area_name')
            ->join("area_information b", "a.area_id = b.area_id", "LEFT")
            ->join("area c", "a.area_id = c.id", "LEFT")
            ->join("user d", "b.created_by = d.id", "LEFT")
            ->where('a.status', 1)
            ->where('b.status', 1)
            ->whereIn('a.area_id', explode(',', $areaArray))
            ->where('b.expired >=', $date)
            ->orderBy('b.urgent', 'DESC')
            ->orderBy('b.expired', 'ASC')
            ->get()->getResultArray();

        if(!empty($model)){
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Informasi tidak ditemukan');
        }
    }

    public function getIssue()
    {
        $areaArray = $this->request->getGet('area_array');
        $date = $this->request->getGet('date');//Y-m-d

        $model = $this->db
            ->table('area_user as a')
            ->select('b.id, b.title, b.message, b.additional_location, b.created_by, d.name created_name, c.area_name')
            ->join("area_issue b", "a.area_id = b.area_id", "LEFT")
            ->join("area c", "a.area_id = c.id", "LEFT")
            ->join("user d", "b.created_by = d.id", "LEFT")
            ->where('a.status', 1)
            ->where('b.status', 1)
            ->whereIn('a.area_id', explode(',', $areaArray))
            ->orderBy('b.updated_at', 'DESC')
            ->get()->getResultArray();

        if(!empty($model)){
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Informasi tidak ditemukan');
        }
    }
}
