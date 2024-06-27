<?php

namespace Master\Controllers;

use App\Controllers\BaseController;

class KelurahanController extends BaseController
{
    public function __construct(){
        $this->data = [
            'table' => 'master_kelurahan',
            'title' => 'Kelurahan',
            'route' => 'master/kelurahan',
            'view' => 'Master\kelurahan'
        ];
    }

    public function get_data()
    {
        $builder = $this->db
            ->table($this->data['table'] . " as a")
            ->select("a.id, a.kelurahan_name, b.provinsi_name, c.kabupaten_name, d.kecamatan_name")
            ->join("master_provinsi as b", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.id, '.', 1), '.', 1) = b.id", "LEFT")
            ->join("master_kabupaten_kota as c", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.id, '.', 2), '.', 2) = c.id", "LEFT")
            ->join("master_kecamatan as d", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.id, '.', 3), '.', 3) = d.id", "LEFT");
            

        $this->datatables->query($builder);
        return $this->datatables->generate()->toJson();
    }

    public function index()
    {
        return $this->templates->generateLayout($this->data['view'] . '\index', $this->data);
    }
}
