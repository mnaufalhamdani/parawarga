<?php

namespace Service\Controllers;

use App\Controllers\ServiceController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class AuthController extends ServiceController
{
    use ResponseTrait;
    public function __construct()
    {
        $this->data = [
            'table' => 'user',
            'title' => 'User',
        ];
    }

    public function login()
    {
        if($this->request->getMethod() == 'POST') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
    
            $model = $this->db
                ->table($this->data['table'] . " as a")
                ->select("a.*, c.name status_keluarga_name, d.name status_pekerjaan_name,
                        e.name status_agama_name, f.name status_nikah_name")
                ->join("master_option as c", "a.status_keluarga = c.id", "LEFT")
                ->join("master_option as d", "a.status_pekerjaan = d.id", "LEFT")
                ->join("master_option as e", "a.status_agama = e.id", "LEFT")
                ->join("master_option as f", "a.status_nikah = f.id", "LEFT")
                ->where('a.username', $username)
                ->where('a.password', $password)
                ->get()->getRowArray();
    
            if($model){//user ditemukan
                if($model['status'] == 0){//belum aktifasi
                    return $this->failUnauthorized('Akun Anda belum aktifasi, silahkan cek email untuk aktifasi');
                }else if($model['status'] == 2){//terblokir
                    return $this->failUnauthorized('Akun Anda telah terblokir');
                }
    
                $model['area'] = $this->db
                    ->table("area_user as a")
                    ->select("a.area_id, a.titik_code, a.role_id, h.role, c.license_code_validation, 
                            c.end_date, c.license_type, c.status,
                            kelurahan_name, provinsi_name, kabupaten_name, kecamatan_name")
                    ->join("area as b", "a.area_id = b.id", "LEFT")
                    ->join("area_license as c", "a.area_id = c.area_id", "LEFT")
                    ->join("master_kelurahan as d", "b.kelurahan_id = d.id", "LEFT")
                    ->join("master_provinsi as e", "SUBSTRING_INDEX(SUBSTRING_INDEX(b.kelurahan_id, '.', 1), '.', 1) = e.id", "LEFT")
                    ->join("master_kabupaten_kota as f", "SUBSTRING_INDEX(SUBSTRING_INDEX(b.kelurahan_id, '.', 2), '.', 2) = f.id", "LEFT")
                    ->join("master_kecamatan as g", "SUBSTRING_INDEX(SUBSTRING_INDEX(b.kelurahan_id, '.', 3), '.', 3) = g.id", "LEFT")
                    ->join("user_role as h", "a.role_id = h.id", "LEFT")
                    ->where('a.user_id', $model['id'])
                    ->where('a.status', 1)
                    ->get()->getResultArray();
                
                if($model['area']){//area ditemukan
                    foreach($model['area'] as $key => $value) {
                        if(empty($value['license_code_validation'])){//license_code_validation null
                            // return $this->fail('Lisensi Area Anda tidak ditemukan');
                            $model['area'][$key]['license_type'] = "BLOCK";
                            $model['area'][$key]['status'] = 0;
                        }else if(date('Y-m-d H:i:s') > $value['end_date']){//tanggal sekarang lebih dari end_date
                            // return $this->fail('Lisensi Area Anda kadaluarsa');
                            $model['area'][$key]['license_type'] = "EXPIRED";
                            $model['area'][$key]['status'] = 0;
                        }
                    }
    
                    /**
                     * =========
                     * SETUP JWT
                     * =========
                     */
                    $key = getenv('jwtKey');
                    $iat = time(); //current timestamp value
                    $exp = $iat + (60 * 60 * 24 * 30); //30 day
    
    
                    $payload = array(
                        "iss" => getenv('CI_ENVIRONMENT'),
                        "aud" => getenv('app.baseURL'),
                        "sub" => $key,
                        "iat" => $iat,
                        "exp" => $exp,
                        "id" => $this->strToBinary($model['id']),
                    );
                     
                    $model['token'] = JWT::encode($payload, $key, 'HS256');
                    
                    return $this->respondSuccess($model);   
                }else {
                    return $this->failNotFound('Area Anda tidak ditemukan');
                }
            }else{
                return $this->failNotFound('Username atau Password salah');
            }
        }else {
            return $this->failForbidden('Request bad method');
        }
    }
}
