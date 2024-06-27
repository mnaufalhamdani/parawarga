<?php

namespace Master\Models;

use CodeIgniter\Model;

class AreaModel extends Model
{
    protected $table               = 'master_area';
    protected $primaryKey          = 'id';
    protected $useAutoIncrement    = false;
    protected $returnType          = 'array';
    protected $useSoftDeletes      = false;
    protected $protectFields       = true;
    protected $allowedFields       = ['id', 'kelurahan_id', 'area_name', 'address', 'phone', 'photo_area', 'created_by', 'updated_by'];
    protected bool $allowEmptyInserts   = true;

    // Dates
    protected $useTimestamps       = true;
    protected $dateFormat          = 'datetime';
    protected $createdField        = 'created_at';
    protected $updatedField        = 'updated_at';
    protected $deletedField        = 'deleted_at';

    // Validation
    protected $validationRules     = [
        'kelurahan_id'  => ['label' => 'Kelurahan', 'rules' => 'required'],
        'area_name'     => ['label' => 'Area', 'rules' => 'required'],
        'address'       => ['label' => 'Alamat', 'rules' => 'required'],
        'phone'         => ['label' => 'No. Telp', 'rules' => 'required'],
    ];
    protected $validationMessages  = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function rules() {
        return $this->validationRules;
    }

    public function getErrorRules() {
        $validation = \Config\Services::validation();
        foreach($this->validationRules as $key => $val) {
            $errorValidation[] = [
                'name'      => $key,
                'message'   => $validation->getError($key)
            ];
        }

        $data['error'] = $errorValidation;

        return json_encode($data);
    }

    public function generateCode()
    {
        $date = date('ymd');
        $total = 0;
        $model = $this
            ->select('count(id) count')
            ->like(['id' => $date])
            ->first();

        if($model['count'] > 0){
            $model = $this
                ->select('id')
                ->like(['id' => $date])
                ->orderBy('id DESC')
                ->first();
            $total = (int)substr($model['id'], -3);
        }
        $code = (string)$date.sprintf("%03s", ($total + 1));
        
        return $code;
    }
}
