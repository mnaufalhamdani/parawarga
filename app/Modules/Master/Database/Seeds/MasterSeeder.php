<?php

namespace Master\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run()
    {
        $masters = [
            'id' => '240530001',
            'area_name' => 'RT.001 / RW.003',
            'kelurahan_id' => '35.73.05.1010',
            'address' => 'Jl. Bantaran',
            'phone' => '089242424',
            'status' => '1',
            'created_at' => '2024-05-22 02:38:49',
            'created_by' => '1',
            'updated_at' => '2024-05-22 02:38:49',
            'updated_by' => '',
        ];
        $this->db->table('master_area')->insert($master);
    }
}
