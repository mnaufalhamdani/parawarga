<?php

namespace Master\Models;

use CodeIgniter\Model;

class OptionTypeModel extends Model
{
    protected $table               = 'master_option_type';
    protected $primaryKey          = 'id';
    protected $useAutoIncrement    = true;
    protected $returnType          = 'array';
    protected $useSoftDeletes      = false;
    protected $protectFields       = true;
    protected $allowedFields       = ['name_type', 'slug'];
    protected bool $allowEmptyInserts   = true;

    // Dates
    protected $useTimestamps       = true;
    protected $dateFormat          = 'datetime';
    protected $createdField        = 'created_at';
    protected $updatedField        = 'updated_at';
    protected $deletedField        = 'deleted_at';

    // Validation
    protected $validationRules     = [
        'name_type'          => ['label' => 'Name Type', 'rules' => 'required'],
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
}
