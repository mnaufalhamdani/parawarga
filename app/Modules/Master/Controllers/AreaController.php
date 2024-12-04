<?php

namespace Master\Controllers;

use App\Controllers\BaseController;
use Master\Models\AreaModel;

class AreaController extends BaseController
{
    public function __construct()
    {
        $this->data = [
            'table' => 'area',
            'title' => 'Area',
            'route' => 'master/area',
            'view' => 'Master\area',
        ];
    }

    public function get_data()
    {
        $builder = $this->db
            ->table($this->data['table'] . " as a")
            ->select("a.id, area_name, address, kelurahan_name")
            ->join("master_kelurahan as b", "a.kelurahan_id = b.id", "LEFT");

        $this->datatables->query($builder);
        $this->datatables->edit('address', function($data) {
            if(empty($data['kelurahan_name'])) {
                return $data['address'];
            }else {
                return $data['address'] . ' (' . $data['kelurahan_name'] . ')';
            }
            
        });
        $this->datatables->add('action', function($data) {
            return $this->generateButton($data);
        });
        return $this->datatables->generate()->toJson();
    }

    public function generateButton($data)
    {
        $html = '<div class="dropdown">' .
            '<button type="button" class="btn btn-icon btn-secondary p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' .
                '<i class="bx bx-message-square-dots"></i></i>' .
            '</button>' .
            '<div class="dropdown-menu">' .
                '<a class="dropdown-item" href="' . base_url($this->data['route']. '/detail/' . base64_encode($data['id'])) . '">' .
                '<button type="button" class="btn"><i class="bx bx-file"></i> Detail</button></a>' .
                '<a class="dropdown-item" href="' . base_url($this->data['route'].'/update/' . base64_encode($data['id'])) . '">' .
                '<button type="button" class="btn"><i class="bx bx-edit-alt"></i> Ubah</button></a>' .
                '<a class="dropdown-item" href="javascript:void(0);">' .
                '<button type="button" class="btn btn-label-delete" onclick="doDelete(this.value)" value="' . base64_encode($data['id']) . '" data-bs-toggle="modal" data-bs-target="#modalDelete"><i class="bx bx-trash me-1"></i> Hapus</button></a>' .
            '</div>' .
        '</div>';

        return $html;
    }

    public function get_data_kabupaten()
    {
        $id = $this->request->getPost('provinsi_id');
        $models = $this->db
            ->table("master_kabupaten_kota")
            ->select("id, kabupaten_name")
            ->like("id", $id . '.')
            ->get()->getResultArray();

        return json_encode($models);
    }

    public function get_data_kecamatan()
    {
        $id = $this->request->getPost('kabupaten_id');
        $models = $this->db
            ->table("master_kecamatan")
            ->select("id, kecamatan_name")
            ->like("id", $id . '.')
            ->get()->getResultArray();

        return json_encode($models);
    }

    public function get_data_kelurahan()
    {
        $id = $this->request->getPost('kecamatan_id');
        $models = $this->db
            ->table("master_kelurahan")
            ->select("id, kelurahan_name")
            ->like("id", $id . '.')
            ->get()->getResultArray();

        return json_encode($models);
    }

    public function index()
    {
        return $this->templates->generateLayout($this->data['view'] . '\index', $this->data);
    }

    public function create()
    {
        $this->data['provinsi'] = $this->db
            ->table("master_provinsi")
            ->select("id, provinsi_name")
            ->get()->getResultArray();

        $this->data['title'] = 'Tambah Data ' . $this->data['title'];
        $this->data['newRecord'] = true;
        
        return $this->templates->generateLayout($this->data['view'] . '\input', $this->data);
    }

    public function onCreate()
    {
        if($this->request->isAJAX() && $this->request->getMethod() == 'POST'){
            $model = model(AreaModel::class);
            $request = $this->request->getPost();

            if(!$this->validate($model->rules())) {
                return $model->getErrorRules();
            }else {
                $request['id'] = $model->generateCode();
         
                //handle file
                $file = $this->request->getFile('photo_area');
                if($file->getError() == 4){
                    $fileName = '/assets/img/default/img_default.png';
                }else{
                    $fileName = $file->getRandomName();
                    $file->move('img/area', $fileName);
                    $fileName = '/img/area/' . $fileName;
                }
                $request['photo_area'] = $fileName;

                if($model->save($request)){
                    session()->setFlashdata('success_msg', 'Data berhasil ditambahkan');
                    $response['success'] = base_url($this->data['route']);
                    return json_encode($response);
                }
            }
        }else {
            // throw new \CodeIgniter\Exceptions\PageNotFoundException('Bad Request');
            session()->setFlashData('error_msg', 'Request Bad Method');
            return redirect()->to($this->data['route']);
        }
    }

    public function update($id)
    {
        $model = $this->db
            ->table($this->data['table'] . " as a")
            ->select("a.id, area_name, a.address, a.phone, photo_area, a.status, 
                a.created_at, a.created_by, a.updated_at, a.updated_by, 
                b.id as kelurahan_id, c.id as provinsi_id, d.id as kabupaten_id, 
                e.id as kecamatan_id")
            ->join("master_kelurahan as b", "a.kelurahan_id = b.id", "LEFT")
            ->join("master_provinsi as c", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.kelurahan_id, '.', 1), '.', 1) = c.id", "LEFT")
            ->join("master_kabupaten_kota as d", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.kelurahan_id, '.', 2), '.', 2) = d.id", "LEFT")
            ->join("master_kecamatan as e", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.kelurahan_id, '.', 3), '.', 3) = e.id", "LEFT")
            ->where('a.id', base64_decode($id))
            ->get()->getRowArray();
        
        if(!empty($model)){
            $this->data['provinsi'] = $this->db
                ->table("master_provinsi")
                ->select("id, provinsi_name")
                ->get()->getResultArray();

            $this->data['title'] = 'Ubah Data ' . $model['area_name'];
            $this->data['model'] = $model;
            $this->data['newRecord'] = false;

            return $this->templates->generateLayout($this->data['view'] . '\input', $this->data);
        }else {
            session()->setFlashData('error_msg', 'Data tidak ditemukan');
            return redirect()->to($this->data['route']);
        }
    }

    public function onUpdate()
    {
        if($this->request->isAJAX() && $this->request->getMethod() == 'POST'){
            $model = model(AreaModel::class);
            $request = $this->request->getPost();

            if(!$this->validate($model->rules())) {
                return $model->getErrorRules();
            }else {
                //handle file
                $photoAreaOld = $this->request->getPost('photo_area_old');
                $file = $this->request->getFile('photo_area');
                if($file->getError() == 4){
                    $fileName = $photoAreaOld;
                }else{
                    $fileName = $file->getRandomName();
                    $file->move('img/area', $fileName);
                    $fileName = '/img/area/' . $fileName;

                    if($photoAreaOld != '/assets/img/default/img_default.png'){
                        @unlink(substr($photoAreaOld, 1));
                    }
                }
                $request['photo_area'] = $fileName;

                if($model->save($request)){
                    session()->setFlashdata('success_msg', 'Data berhasil diubah');
                    $response['success'] = base_url($this->data['route']);
                    return json_encode($response);
                }
            }            
        }else {
            // throw new \CodeIgniter\Exceptions\PageNotFoundException('Bad Request');
            session()->setFlashData('error_msg', 'Request Bad Method');
            return redirect()->to($this->data['route']);
        }
    }

    public function onDelete()
    {
        if($this->request->getMethod() == 'DELETE'){
            $request = $this->request->getPost();
            $model = model(AreaModel::class);
            $data = $model->find(base64_decode($request['id']));

            if($model->delete(base64_decode($request['id']))){
                if($data['photo_area'] != '/assets/img/default/img_default.png'){
                    @unlink(substr($data['photo_area'], 1));
                }

                return redirect()->to($this->data['route'])->with('info_msg', 'Data berhasil dihapus');
            }
        }else {
            // throw new \CodeIgniter\Exceptions\PageNotFoundException('Bad Request');
            session()->setFlashData('error_msg', 'Request Bad Method');
            return redirect()->to($this->data['route']);
        }
    }

    public function detail($id)
    {
        $model = $this->db
            ->table($this->data['table'] . " as a")
            ->select("a.id, area_name, a.address, a.phone, photo_area, a.created_at, f.name, a.updated_at, kelurahan_name, provinsi_name, kabupaten_name, kecamatan_name")
            ->join("master_kelurahan as b", "a.kelurahan_id = b.id", "LEFT")
            ->join("master_provinsi as c", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.kelurahan_id, '.', 1), '.', 1) = c.id", "LEFT")
            ->join("master_kabupaten_kota as d", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.kelurahan_id, '.', 2), '.', 2) = d.id", "LEFT")
            ->join("master_kecamatan as e", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.kelurahan_id, '.', 3), '.', 3) = e.id", "LEFT")
            ->join("user as f", "a.created_by = f.id", "LEFT")
            ->where('a.id', base64_decode($id))
            ->get()->getRowArray();

        if(!empty($model)){
            $model['id'] = $id;
            $this->data['title'] = 'Detail Data ' . $model['area_name'];
            $this->data['model'] = $model;

            return $this->templates->generateLayout($this->data['view'] . '\detail', $this->data);
        }else {
            session()->setFlashData('error_msg', 'Data tidak ditemukan');
            return redirect()->to($this->data['route']);
        }
    }
}
