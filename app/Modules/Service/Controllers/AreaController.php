<?php

namespace Service\Controllers;

use App\Controllers\ServiceController;
use CodeIgniter\API\ResponseTrait;
use Service\Models\AreaModel;

class AreaController extends ServiceController
{
    use ResponseTrait;

    /**
     * create new area and save in database
     * @body $data json encode from API
     * @return general response
     */
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

    /**
     * get area management data related to the user's requested area
     * @param $area_array data with coma, $date (Y-m-d)
     * @return custom
     */
    public function getAreaManagement()
    {
        $areaArray = $this->request->getGet('area_array');
        $date = $this->request->getGet('date');

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

    /**
     * get data unit related to the user area that requested
     * @param $area_array data with coma, $empty (optional) to display empty unit (1), $user_id (optional) to display units based on user requests
     * @return custom
     */
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

    /**
     * get data of all registered bank accounts for the request area
     * @param $area_id one area request
     * @return custom
     */
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

    /**
     * get data information to display all information related to the request area
     * @param $area_array data with coma, $date (Y-m-d)
     * @return custom
     */
    public function getInformation()
    {
        $areaArray = $this->request->getGet('area_array');
        $date = $this->request->getGet('date');

        $model = $this->db
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
            ->get()->getResultArray();

        if(!empty($model)){
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Informasi tidak ditemukan');
        }
    }

    /**
     * get data information detail based on request id
     * @param $id is information id, $user_id from token
     * @return custom
     */
    public function getInformationDetail()
    {
        $id = $this->request->getGet('id');
        $userId = $this->request->user->user_id;

        $model = $this->db
            ->table('area_information as a')
            ->select("a.id, 
                a.title, 
                a.message, 
                a.expired, 
                a.urgent, 
                a.created_by, 
                c.name created_name, 
                b.area_name,
                DATE_FORMAT(a.updated_at, '%d %M %Y %H:%i') updated_at")
            ->join("area b", "a.area_id = b.id", "LEFT")
            ->join("user c", "a.created_by = c.id", "LEFT")
            ->where('a.status', 1)
            ->where('a.id', $id)
            ->get()->getRowArray();

        if(!empty($model)){
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Informasi tidak ditemukan');
        }
    }

    /**
     * get data issue to display all issue related to the request area
     * @param $area_array data with coma, $date (Y-m-d)
     * @return custom
     */
    public function getIssue()
    {
        $areaArray = $this->request->getGet('area_array');
        $date = $this->request->getGet('date');//Y-m-d

        $model = $this->db
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
            ->get()->getResultArray();

        if(!empty($model)){
            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Informasi tidak ditemukan');
        }
    }

    /**
     * get data issue detail based on request id
     * @param $id is information id, $user_id from token
     * @return custom
     */
    public function getIssueDetail()
    {
        $id = $this->request->getGet('id');
        $userId = $this->request->user->user_id;

        $model = $this->db
            ->table('area_issue as a')
            ->select("a.id, 
                a.title, 
                a.message, 
                a.latitude, 
                a.longitude, 
                a.generate_location, 
                a.additional_location, 
                a.created_by, 
                c.name created_name, 
                b.area_name,
                DATE_FORMAT(a.updated_at, '%d %M %Y %H:%i') updated_at")
            ->join("area b", "a.area_id = b.id", "LEFT")
            ->join("user c", "a.created_by = c.id", "LEFT")
            ->where('a.status', 1)
            ->where('a.id', $id)
            ->get()->getRowArray();

        if(!empty($model)){
            $model['history'] = $this->db
                ->table('area_issue_history as a')
                ->select("a.issue_id, 
                    a.urutan, 
                    a.status_id, 
                    b.name status_name, 
                    a.message, 
                    DATE_FORMAT(a.updated_at, '%d %M %Y %H:%i') updated_at")
                ->join("master_option b", "a.status_id = b.id", "LEFT")
                ->where('a.status', 1)
                ->where('a.issue_id', $model['id'])
                ->orderBy('a.urutan', 'ASC')
                ->get()->getResultArray();

            $model['attachment'] = $this->db
                ->table('area_issue_attachment as a')
                ->select("a.issue_id, 
                    a.urutan, 
                    a.urutan_issue_history, 
                    a.status_id, 
                    b.name status_name, 
                    CONCAT('".base_url()."', 'public/', a.attachment) attachment,
                    DATE_FORMAT(a.updated_at, '%d %M %Y %H:%i') updated_at")
                ->join("master_option b", "a.status_id = b.id", "LEFT")
                ->where('a.status', 1)
                ->where('a.issue_id', $model['id'])
                ->orderBy('a.urutan', 'ASC')
                ->get()->getResultArray();

            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Issue tidak ditemukan');
        }
    }
}
