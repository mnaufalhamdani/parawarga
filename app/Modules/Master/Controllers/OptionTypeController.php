<?php

namespace Master\Controllers;

use App\Controllers\BaseController;
use Master\Models\OptionTypeModel;
use App\Models\AuthMenuModel;

class OptionTypeController extends BaseController
{
    public function __construct()
    {
        $this->data = [
            'table' => 'master_option_type',
            'title' => 'Option Type',
            'route' => 'master/option-type',
            'view'  => 'Master\option-type',
        ];
    }

    public function get_data()
    {
        $builder = $this->db
            ->table($this->data['table'])
            ->select("id, name_type");

        $this->datatables->query($builder);
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
        $this->data['title'] = 'Tambah Data ' . $this->data['title'];
        $this->data['newRecord'] = true;
        
        return $this->templates->generateLayout($this->data['view'] . '\input', $this->data);
    }

    public function onCreate()
    {
        if($this->request->isAJAX() && $this->request->getMethod() == 'POST'){
            $model = model(OptionTypeModel::class);
            $request = $this->request->getPost();

            if(!$this->validate($model->rules())) {
                return $model->getErrorRules();
            }else {
                $request['slug'] = mb_url_title($request['name_type'], '-', true);
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
        $model = model(OptionTypeModel::class)
            ->find(base64_decode($id));
        
        if(!empty($model)){
            $this->data['title'] = 'Ubah Data ' . $model['name_type'];
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
            $model = model(OptionTypeModel::class);
            $request = $this->request->getPost();

            if(!$this->validate($model->rules())) {
                return $model->getErrorRules();
            }else {
                $request['slug'] = mb_url_title($request['name_type'], '-', true);
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
            $model = model(OptionTypeModel::class);

            if($model->delete(base64_decode($request['id']))){
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
        $model = model(OptionTypeModel::class)
            ->find(base64_decode($id));
            
        if(!empty($model)){
            $model['id'] = $id;
            $this->data['title'] = 'Detail Data ' . $model['name_type'];
            $this->data['model'] = $model;

            return $this->templates->generateLayout($this->data['view'] . '\detail', $this->data);
        }else {
            session()->setFlashData('error_msg', 'Data tidak ditemukan');
            return redirect()->to($this->data['route']);
        }
    }
}
