<?php

namespace Service\Models;

use CodeIgniter\Model;

class ServicesModel extends Model
{
    protected $table               = 'services';
    protected $primaryKey          = 'id';
    protected $useAutoIncrement    = true;
    protected $returnType          = 'array';
    protected $useSoftDeletes      = false;
    protected $protectFields       = true;
    protected $allowedFields       = [];
    protected bool $allowEmptyInserts   = true;

    // Dates
    protected $useTimestamps       = true;
    protected $dateFormat          = 'datetime';
    protected $createdField        = 'created_at';
    protected $updatedField        = 'updated_at';
    protected $deletedField        = 'deleted_at';

    // Validation
    protected $validationRules     = [
        // 'active'        => 'trim|permit_empty|in_list[0,1]',
        // 'title'         => 'trim|required|string|min_length[3]|max_length[32]|is_unique[permissions.title]',
        // 'slug'          => 'trim|permit_empty|max_length[32]|is_unique[permissions.slug]',
        // 'description'   => 'trim|permit_empty|max_length[255]',
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
