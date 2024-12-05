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
                ->select("a.*, b.role, c.name status_keluarga_name, d.name status_pekerjaan_name,
                        e.name status_agama_name, f.name status_nikah_name")
                ->join("user_role as b", "a.role_id = b.id", "LEFT")
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
                    ->table("area as a")
                    ->select("a.*, b.license_code_validation, b.end_date, b.license_type,
                            kelurahan_name, provinsi_name, kabupaten_name, kecamatan_name")
                    ->join("area_license as b", "a.id = b.area_id", "LEFT")
                    ->join("master_kelurahan as c", "a.kelurahan_id = c.id", "LEFT")
                    ->join("master_provinsi as d", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.kelurahan_id, '.', 1), '.', 1) = d.id", "LEFT")
                    ->join("master_kabupaten_kota as e", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.kelurahan_id, '.', 2), '.', 2) = e.id", "LEFT")
                    ->join("master_kecamatan as f", "SUBSTRING_INDEX(SUBSTRING_INDEX(a.kelurahan_id, '.', 3), '.', 3) = f.id", "LEFT")
                    ->where('a.id', $model['area_code'])
                    ->where('a.status', 1)
                    ->get()->getRowArray();
                
                if($model['area']){//area ditemukan
                    if(empty($model['area']['license_code_validation'])){//license_code_validation null
                        return $this->fail('Lisensi Area Anda tidak ditemukan');
                    }else if(date('Y-m-d H:i:s') > $model['area']['end_date']){//tanggal sekarang lebih dari end_date
                        return $this->fail('Lisensi Area Anda kadaluarsa');
                    }
    
                    /**
                     * =========
                     * SETUP JWT
                     * =========
                     */
                    $key = getenv('jwtKey');
                    $iat = time(); //current timestamp value
                    $exp = $iat + (60 * 60 * 24 * 1); //1 day
    
    
                    $payload = array(
                        "iss" => getenv('CI_ENVIRONMENT'),
                        "aud" => getenv('app.baseURL'),
                        "sub" => $key,
                        "iat" => $iat,
                        "exp" => $exp,
                        "id" => $model['id'],
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
