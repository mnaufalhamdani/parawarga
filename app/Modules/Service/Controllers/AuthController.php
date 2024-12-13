<?php

namespace Service\Controllers;

use App\Controllers\ServiceController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Service\Models\AreaCodeGenerateModel;
use Service\Models\UserModel;
use Service\Models\AreaUserModel;

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

     /**
     * get data user, area, area license, token jwt
     * @body username, password
     * @return custom
     */
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
                    ->select("a.area_id, b.area_name, b.address, a.titik_code, a.role_id, h.role, c.license_code_validation, 
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
                            $model['area'][$key]['license_type'] = "BLOCK";
                            $model['area'][$key]['status'] = 0;
                        }else if($value['license_type'] == 'TRIAL' && $value['end_date'] < date('Y-m-d H:i:s')){//tanggal sekarang lebih dari end_date
                            $model['area'][$key]['license_type'] = "EXPIRED";
                        }else if($value['license_type'] == 'EXPIRED' && date('Y-m-d H:i:s', strtotime("+6 months", strtotime($value['end_date']))) < date('Y-m-d H:i:s')){//tanggal sekarang lebih dari end_date + 6 bulan
                            $model['area'][$key]['license_type'] = "BLOCK";
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

     /**
     * Encrypt the area only 8 character
     * @body $area_id, $user_id
     * @return custom
     */
    public function encodeArea() {                
        $areaId = $this->request->getPost('area_id');
        $userId = $this->request->getPost('user_id');
        $datetime = date('Y-m-d H:i:s');

        if(empty($areaId) || empty($userId)){
            return $this->failForbidden('Area atau User tidak diketahui');
        }

        $plainText = $areaId . '_' . $userId . '_' . $datetime;

        $code = substr($this->strEncode($plainText), 0, 8);

        $data = $this->db
            ->table('area_code_generate as a')
            ->select('a.code, a.area_id, a.created_by')
            ->where('a.area_id', $areaId)
            ->where('a.created_by', $userId)
            ->orderBy('a.created_at', 'DESC')
            ->get()->getRowArray();
        
        if(empty($data)) {
            $model = model(AreaCodeGenerateModel::class);
            $data['code'] = $code;
            $data['area_id'] = $areaId;
            $data['created_by'] = $userId;
            if (!$model->save($data)){
                return $this->fail('terdapat kesalahan saat menyimpan data');    
            }
        }

        if($data){
            return $this->respondSuccess($data);  
        }else {
            return $this->failNotFound('Data tidak ditemukan');
        }
    }

    /**
     * Verify code of area
     * @body $area_encoded
     * @return custom
     */
    public function verifyEncodeArea() {
        $key = getenv('encryption.key');
        $areaEncoded = $this->request->getPost('area_encoded');
        
        if(empty($areaEncoded)){
            return $this->failForbidden('Kode tidak diketahui');
        }

        $model = $this->db
            ->table('area_code_generate as a')
            ->select('a.code area_generate, a.area_id, b.area_name, c.license_code_validation, 
                        c.end_date, c.license_type, c.status, kelurahan_name, provinsi_name, kabupaten_name, kecamatan_name')
            ->join('area b', 'a.area_id = b.id', 'LEFT')
            ->join('area_license c', 'a.area_id = c.area_id', 'LEFT')
            ->join("master_kelurahan as d", "b.kelurahan_id = d.id", "LEFT")
            ->join("master_provinsi as e", "SUBSTRING_INDEX(SUBSTRING_INDEX(b.kelurahan_id, '.', 1), '.', 1) = e.id", "LEFT")
            ->join("master_kabupaten_kota as f", "SUBSTRING_INDEX(SUBSTRING_INDEX(b.kelurahan_id, '.', 2), '.', 2) = f.id", "LEFT")
            ->join("master_kecamatan as g", "SUBSTRING_INDEX(SUBSTRING_INDEX(b.kelurahan_id, '.', 3), '.', 3) = g.id", "LEFT")
            ->where('a.code', $areaEncoded)
            ->orderBy('a.created_at', 'DESC')
            ->get()->getRowArray();
            
        if(!empty($model)){
            if(empty($model['license_code_validation'])){//license_code_validation null
                return $this->failNotFound('License tidak diketahui');
            }else if($model['license_type'] == 'TRIAL' && $model['end_date'] < date('Y-m-d H:i:s')){//tanggal sekarang lebih dari end_date
                $model['license_type'] = "EXPIRED";
            }else if($model['license_type'] == 'EXPIRED' && date('Y-m-d H:i:s', strtotime("+6 months", strtotime($model['end_date']))) < date('Y-m-d H:i:s')){//tanggal sekarang lebih dari end_date + 6 bulan
                return $this->failNotFound('Area terblokir');
            }

            return $this->respondSuccess($model);  
        }else {
            return $this->failNotFound('Area tidak ditemukan');
        }
    }

    /**
     * Verify nik of user
     * @body $area_id
     * @return message
     */
    public function verifyNik() {
        $nik = $this->request->getPost('nik');
            
        if(empty($nik)){
            return $this->failForbidden('NIK tidak diketahui');
        }

        $model = $this->db
            ->table('user as a')
            ->select('a.nik')
            ->where('a.status', 1)
            ->where('a.nik', $nik)
            ->orderBy('a.created_at', 'DESC')
            ->get()->getRowArray();
            
        if(empty($model)){
            return $this->respondSuccess(['message' => 'NIK siap digunakan']);  
        }else {
            return $this->fail('NIK telah terdaftar, silahkan masuk dengan NIK yang terdaftar');
        }
    }

    /**
     * daftar user
     * @body $data
     * @return message
     */
    public function register() {
        try{
            if($this->request->getMethod() != 'POST'){
                return $this->failForbidden('Request bad method');
            }

            $this->db->transStart();
            $request = $this->request->getPost('data');
            $data = (array) json_decode($request);
            $data['nik'] = $this->strToBinary($data['nik']);

            $model = $this->db
                ->table('user as a')
                ->select('a.nik')
                ->where('a.nik', $data['nik'])
                ->orWhere('a.email', $data['email'])
                ->orderBy('a.created_at', 'DESC')
                ->get()->getRowArray();
            
            if(!empty($model)){
                return $this->fail('NIK ata Email telah terdaftar, silahkan masuk dengan email yang terdaftar');
            }

            $model = model(UserModel::class);
            $data['password'] = $this->strEncode($data['password']);

            //handle file
            if (preg_match('/^data:(\w+\/\w+);base64,/', $data['photo'], $matches)) {
                $fileType = $matches[1];
                $base64String = substr($data['photo'], strpos($data['photo'], ',') + 1);
                $fileData = base64_decode($base64String);

                if($fileData === false){
                    return $this->fail('gagal mengolah foto');
                }

                $fileExtension = explode('/', $fileType)[1];
                $fileName = uniqid() . '.' . $fileExtension;

                // Save the file
                $filePath = 'img/user/' . $fileName;
                if (!file_put_contents($filePath, $fileData)) {
                    return $this->fail('gagal menyimpan foto');
                }

                $data['photo'] = $filePath;
            } 

            if(!$model->save($data)){
                return $this->fail('Gagal registrasi user');   
            }
            $data['id'] = $model->insertID();

            //check area user
            $checkAreaUser = $this->db
                ->table('area_code_generate as a')
                ->select('a.code, a.area_id, b.user_id')
                ->join('area_user b', 'a.area_id = b.area_id AND b.user_id = '. $data['id'], 'LEFT')
                ->where('a.code', $data['area_generate'])
                ->orderBy('a.created_at', 'DESC')
                ->get()->getRowArray();

            if(empty($checkAreaUser)) {
                return $this->failNotFound('Area tidak ditemukan');   
            }

            if(!empty($checkAreaUser['user_id'])){
                return $this->fail('Data Anda telah terdaftar di area ini');   
            }

            $model = model(AreaUserModel::class);
            $areaUser['id'] = $model->generateCode();
            $areaUser['user_id'] = $data['id'];
            $areaUser['created_by'] = $data['id'];
            $areaUser['area_id'] = $checkAreaUser['area_id'];
            $areaUser['area_generate'] = $data['area_generate'];
            $areaUser['role_id'] = 3;
            $areaUser['status'] = 0;

            if(!$model->save($areaUser)){
                return $this->fail('Gagal mendaftarkan Area');   
            }

            $plainText = $areaUser['user_id'] . "." . $areaUser['id'] . "." . date('Y-m-d H:i:s');
            $encode = strtr(base64_encode($plainText), '+/=', '-_,');
            $data['activation'] = base_url().'service/userActivation?code=' . $encode;

            $this->db->transComplete();
            return $this->respondSuccess($data);
        }catch (\Exception $e) {
            return $this->failForbidden($e->getMessage());
        }
    }

    /**
     * activation of user
     * @body $code
     * @return message
     */
    public function userActivation() {
        $this->db->transStart();
        $code = $this->request->getGet('code');
            
        if(empty($code)){
            return $this->failForbidden('Kode tidak diketahui');
        }
        $decode = base64_decode(strtr($code, '-_,', '+/='));
        $splitCode  = explode('.', $decode);

        $model = model(UserModel::class);
        $data['id'] = $splitCode[0];
        $data['activated_at'] = date('Y-m-d H:i:s');
        $data['status'] = 1;
        if(!$model->save($data)){
            return $this->fail('Gagal aktivasi user');   
        }

        $model = model(AreaUserModel::class);
        $data['id'] = $splitCode[1];
        $data['status'] = 1;
        if(!$model->save($data)){
            return $this->fail('Gagal aktivasi area user');   
        }
        
        $this->db->transComplete();
        return $this->respondSuccess(['message' => 'User telah di aktivasi']);  
    }
}