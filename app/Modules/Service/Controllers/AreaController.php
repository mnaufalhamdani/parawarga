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
        $areaId = $this->request->getGet('areaId');
        
        $model = $this->db
            ->table('area_management as a')
            ->select('a.*')
            ->where('area_id', $areaId)
            ->where('start_date <=', date('Y-m-d'))
            ->where('end_date >=', date('Y-m-d'))
            ->orderBy('id', 'DESC')
            ->get()->getRowArray();

        if($model){
            $model['detail'] = $this->db
                ->table('area_management_detail as a')
                ->select('a.*, b.name position_name, c.name')
                ->join('master_option b', 'a.management_position = b.id', 'LEFT')
                ->join('user c', 'a.user_id = c.id', 'LEFT')
                ->where('management_id', $model['id'])
                ->get()->getResultArray();
            
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
}
