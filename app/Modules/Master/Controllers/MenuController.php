<?php

namespace Master\Controllers;

use App\Controllers\BaseController;
use Master\Models\MenuModel;

class MenuController extends BaseController
{
    public function __construct(){
        $this->data = [
            'table' => 'master_menu',
            'title' => 'Menu',
            'route' => 'master/menu',
            'view' => 'Master\menu',
        ];
    }

    public function get_data()
    {
        $builder = $this->db
            ->table($this->data['table'] . " as a")
            ->select("a.id, a.name, level, type_id, b.name as option_name")
            ->join("master_option as b", "a.type_id = b.id", "LEFT");

        $this->datatables->query($builder);
        $this->datatables->edit('level', function($data) {
            if($data['level'] == 1){
                return 'Menu';
            }
            else if($data['level'] == 2){
                return 'Sub Menu';
            }
        });
        $this->datatables->edit('type_id', function($data) {
            if($data['type_id'] == 1){
                return '<span class="badge bg-label-dark"> ' . $data['option_name'] . ' </span>';
            }
            else if($data['type_id'] == 2){
                return '<span class="badge bg-label-info"> ' . $data['option_name'] . ' </span>';
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

    public function index()
    {
        return $this->templates->generateLayout($this->data['view'] . '\index', $this->data);
    }

    public function create()
    {
        $parent = model(MenuModel::class)
            ->where(['status' => 1, 'level' => 1])
            ->find();

        $this->data['type'] = $this->db
            ->table("master_option")
            ->select("id, name")
            ->where("option_type_id", 1)
            ->get()->getResultArray();
        
        $this->data['title'] = 'Tambah Data ' . $this->data['title'];
        $this->data['parent'] = $parent;
        $this->data['newRecord'] = true;

        return $this->templates->generateLayout($this->data['view'] . '\input', $this->data);
    }

    public function onCreate()
    {
        if($this->request->isAJAX() && $this->request->getMethod() == 'POST'){
            $model = model(MenuModel::class);
            $request = $this->request->getPost();

            if(!$this->validate($model->rules())) {
                return $model->getErrorRules();
            }else {
                $request['slug'] = mb_url_title($request['name'], '-', true);
                $request['level'] = 1;
                if($request['parent_id'] != 0){
                    $request['level'] = 2;
                }

                if($model->save($request)){
                    session()->setFlashdata('success_msg', 'Data berhasil ditambahkan');
                    $response['success'] = base_url($this->data['route']);
                    return json_encode($response);
                }
            }
        }else {
            session()->setFlashData('error_msg', 'Request Bad Method');
            return redirect()->to($this->data['route']);
        }
    }

    public function update($id)
    {
        $model = model(MenuModel::class)
            ->find(base64_decode($id));
        
        if(!empty($model)){
            $parent = model(MenuModel::class)
            ->where(['status' => 1, 'level' => 1])
            ->find();

            $this->data['type'] = $this->db
                ->table("master_option")
                ->select("id, name")
                ->where("option_type_id", 1)
                ->get()->getResultArray();
            
            $this->data['title'] = 'Ubah Data ' . $model['name'];
            $this->data['parent'] = $parent;
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
            $model = model(MenuModel::class);
            $request = $this->request->getPost();

            if(!$this->validate($model->rules())) {
                return $model->getErrorRules();
            }else {
                $request['slug'] = url_title($request['name'], '-', true);
                $request['level'] = 1;
                if($request['parent_id'] != 0){
                    $request['level'] = 2;
                }
                
                if($model->save($request)){
                    session()->setFlashdata('success_msg', 'Data berhasil diubah');
                    $response['success'] = base_url($this->data['route']);
                    return json_encode($response);
                }
            }            
        }else {
            session()->setFlashData('error_msg', 'Request Bad Method');
            return redirect()->to($this->data['route']);
        }
    }

    public function onDelete()
    {
        if($this->request->getMethod() == 'DELETE'){
            $request = $this->request->getPost();
            $model = model(MenuModel::class);

            if($model->delete(base64_decode($request['id']))){
                return redirect()->to($this->data['route'])->with('info_msg', 'Data berhasil dihapus');
            }
        }else {
            session()->setFlashData('error_msg', 'Request Bad Method');
            return redirect()->to($this->data['route']);
        }
    }

    public function detail($id)
    {
        $model = $this->db
            ->table($this->data['table'] . " a")
            ->select("a.*, b.name parent_name, c.name as option_name")
            ->join($this->data['table'] . " b", "b.id = a.parent_id", "LEFT")
            ->join("master_option as c", "a.type_id = c.id", "LEFT")
            ->where("a.id", base64_decode($id))
            ->get()->getRowArray();
            
        if(!empty($model)){
            $model['id'] = $id;
            $this->data['title'] = 'Detail Data ' . $model['name'];
            $this->data['model'] = $model;

            return $this->templates->generateLayout($this->data['view'] . '\detail', $this->data);
        }else {
            session()->setFlashData('error_msg', 'Data tidak ditemukan');
            return redirect()->to($this->data['route']);
        }
    }
}
