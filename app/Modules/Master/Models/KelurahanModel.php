<?php

namespace Master\Models;

use CodeIgniter\Model;

class KelurahanModel extends Model
{
    protected $table               = 'kelurahan';
    protected $primaryKey          = 'id';
    protected $useAutoIncrement    = false;
    protected $returnType          = 'array';
    protected $useSoftDeletes      = false;
    protected $protectFields       = true;
    protected $allowedFields       = [];

    protected bool $allowEmptyInserts   = false;

    // Dates
    protected $useTimestamps       = false;
    protected $dateFormat          = 'datetime';
    protected $createdField        = 'created_at';
    protected $updatedField        = 'updated_at';
    protected $deletedField        = 'deleted_at';

    // Validation
    protected $validationRules     = [];
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
