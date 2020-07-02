<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 3/24/2020
 * Time: 3:48 PM
 */

namespace App\Services;


use App\Repositories\Users\UserInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class UserService extends BaseApi
{
    private $user;
    private $common;
    public function __construct(UserInterface $userRepository, CommonService $common)
    {
        $this->user = $userRepository;
        $this->common = $common;
    }

    public function getPassword($input) {
        $msisdn = $input['param']['msisdn'];
        $response = [];
        $response['password'] = $this->common->generateRandomString();
        $response['msisdn'] = $msisdn;
        try{
            $result = $this->user->getDataByMsisdn($msisdn);
            if(!empty($result)){
                $response['password'] = $result->password;
                if(empty($response['password'])){
                    $response['password'] = $this->common->generateRandomString();
                    $this->user->updatePassword($response);
                }
                return CommonConstant::responseSuccess($response, $input );
            }else{
                $res = $this->user->signin($response);
                if( $res ){
                    return CommonConstant::responseSuccess($response, $input );
                }else{
                    return CommonConstant::responseError($input,Constant::STATUS_ERROR, Constant::ARRAY_MESSAGE_ERROR[500]);
                }
            }
        }catch (Exception $e){
            Log::error($e->getMessage());
            return CommonConstant::responseError($input,Constant::STATUS_ERROR, $e->getMessage());
        }
    }

    public function forgetPassword($input) {
        try{
            $result = $this->user->getDataByMsisdn($input['msisdn']);
            if(!empty($result)){
                $input['password'] = $this->common->generateRandomString();
                $isCheck = $this->user->forgetPassword($input);
                if($isCheck){
                    $checkSend = $this->sendMtVas($input);
                    if($checkSend)
                        return CommonConstant::responseSuccess([], $input );
                    else
                        return CommonConstant::responseError($input, Constant::STATUS_ERROR_API_VAS, Constant::ARRAY_MESSAGE_ERROR[600]);
                }
            }else {
                return CommonConstant::responseError($input, Constant::STATUS_ERROR_NO_EXIT, Constant::ARRAY_MESSAGE_ERROR[Constant::STATUS_ERROR_NO_EXIT]);
            }
        }catch (Exception $e){
            Log::error($e->getMessage());
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR[500]);
        }

    }

    /**
     * @param $input
     * @return mixed
     */
    public function signin($input){
        try{
            $result = $this->user->getDataByMsisdn($input['msisdn']);
            if(!empty($result)){
                return CommonConstant::responseError($input, Constant::STATUS_ERROR, Constant::ARRAY_MESSAGE_ERROR[Constant::STATUS_ERROR]);
            } else {
                try{
                    $input['password'] = $this->common->generateRandomString();
                    $response = $this->user->signin($input);
                    if($response){
                        $checkSend = $this->sendMtVas($input);
                        if($checkSend)
                            return CommonConstant::responseSuccess([], $input );
                        else
                            return CommonConstant::responseError($input, Constant::STATUS_ERROR_API_VAS, Constant::ARRAY_MESSAGE_ERROR[600]);
                    }
                }catch (Exception $e){
                    Log::error($e->getMessage());
                    return CommonConstant::responseError($input, Constant::STATUS_ERROR, Constant::ARRAY_MESSAGE_ERROR[500]);
                }
            }
        }catch (Exception $exception){
            Log::error($exception->getMessage());
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR[500]);
        }

    }
    public function login($input) {
        try{
            $input['session'] = $this->getKeySesssion();
            $data = $this->user->getDataByMsisdn($input['msisdn']);
            if($input['login_type'] == Constant::LOGIN_3G) {
                $input['password'] = (!empty($data)) ? $data->passwrod : $this->common->generateRandomString();
                $this->user->login($input);
                $result = $this->user->getUserBySession($input['session']);
                return CommonConstant::responseSuccess($result, $input, Constant::ARRAY_MESSAGE_SUCCESS['login_success']);
            } else {
                if(!empty($data)) {
                    if($input['password'] == $data->password){
                        $this->user->login($input);
                        $result = $this->user->getUserBySession($input['session']);
                        return CommonConstant::responseSuccess($result, $input, Constant::ARRAY_MESSAGE_SUCCESS['login_success']);
                    }else{
                        return CommonConstant::responseError($input, Constant::STATUS_ERROR ,Constant::ARRAY_MESSAGE_ERROR['password_error']);
                    }
                } else {
                    return CommonConstant::responseError($input,Constant::STATUS_ERROR, Constant::ARRAY_MESSAGE_ERROR['msisdn_error']);
                }
            }
        }catch (Exception $e){
            Log::error($e->getMessage());
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR[500]);
        }
    }

    public function logout($input) {
       try{
           $objUser = $this->user->checkExitSession($input['session']);
           if(!empty($objUser)){
               $this->user->logout($input);
               return CommonConstant::responseSuccess([], $input, Constant::ARRAY_MESSAGE_SUCCESS['logout_success']);
           }else {
               return CommonConstant::responseError($input, Constant::STATUS_ERROR_SESSION, Constant::ARRAY_MESSAGE_ERROR['session_not_exit']);
           }
       }catch (Exception $e){
           Log::error($e->getMessage());
           return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR[500]);
        }
    }

    public function changePassword($input) {
        try{
            $objUser = $this->user->checkExitSession($input['session']);
            if(!empty($objUser)){
                $this->user->changePassword($input);
                return CommonConstant::responseSuccess([],$input, Constant::ARRAY_MESSAGE_SUCCESS['chang_password_success']);
            }else {
                return CommonConstant::responseError($input, Constant::STATUS_ERROR_SESSION, Constant::ARRAY_MESSAGE_ERROR['session_not_exit']);
            }
        }catch (Exception $e){
            Log::error($e->getMessage());
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR[500]);
        }
    }

    public function getUserInfo($input) {
        try{
            $res = $this->user->getUserBySession($input['session']);
            if( $res ) {
                return CommonConstant::responseSuccess( $res , $input, Constant::ARRAY_MESSAGE_SUCCESS['success']);
            } else {
                return CommonConstant::responseError($input, Constant::STATUS_ERROR, Constant::ARRAY_MESSAGE_ERROR['error']);
            }
        }catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR[500]);
        }

    }

    public function updateUserInfo($input) {
        try{
            if(isset($input['avatar']) && !empty($input['avatar'])){
                $input['avatar']= $this->convertStringToImg($input);
            }
            $res = $this->user->updateUserInfo($input);
            if( $res ) {
                return CommonConstant::responseSuccess($res, $input, Constant::ARRAY_MESSAGE_SUCCESS['update_user_info_success']);
            } else {
                return CommonConstant::responseError($input, Constant::STATUS_ERROR, Constant::ARRAY_MESSAGE_ERROR['update_user_info_error']);
            }
        }catch (Exception $e){
            Log::error($e->getMessage());
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR[500]);
        }
    }

    public function checkPackageStatus($input) {
        try{
            $data = $this->user->checkExitSession($input['session']);
            if(!empty($data)){
                $result = $this->getPackageDetail($data->msisdn);
                if(!$result['response']) {
                    return CommonConstant::responseError($input,Constant::STATUS_ERROR_API_VAS, Constant::ARRAY_MESSAGE_ERROR[600]);
                }else {
                    $dataDetail = $this->processDataSubTransaction($result['data']);
                    return CommonConstant::responseSuccess($dataDetail, Constant::STATUS_SUCCESS);
                }
            } else {
                return CommonConstant::responseError($input, Constant::STATUS_ERROR_SESSION, Constant::ARRAY_MESSAGE_ERROR['session_not_exit']);
            }
        }catch (Exception $e){
            Log::error($e->getMessage());
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR[500]);
        }
    }

    public function getPackageInfo($input) {
        try{
            $data = $this->user->getUserBySession($input['session']);
            if(!empty($data)){
                $msisdn = $data->msisdn;
                $result = $this->getPackageDetail($msisdn);
                if($result['response']){
                    $dataOutPut = [];
                    foreach ($result['data'] as $item){
                        $userInfo = Constant::PACKAGE_INFO;
                        $userInfo['status'] = $item['status'] ?? null;
                        $userInfo['msisdn'] = $item['msisdn'] ?? null;
                        $userInfo['expireTime'] = isset($item['expireTime']) ? Carbon::createFromFormat('H:i:s d/m/Y',$item['expireTime'])->format('d/m/Y') : null;
                        array_push($dataOutPut, $userInfo);
                    }
                    return CommonConstant::responseSuccess($dataOutPut, $input, Constant::ARRAY_MESSAGE_SUCCESS['success']);
                } else {
                    return CommonConstant::responseError($input,Constant::STATUS_ERROR_API_VAS, Constant::ARRAY_MESSAGE_ERROR[600]);
                }
            } else {
                return CommonConstant::responseError($input, Constant::STATUS_ERROR_SESSION, Constant::ARRAY_MESSAGE_ERROR['session_not_exit']);
            }
        }catch (Exception $e){
            Log::error($e->getMessage());
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR[500]);
        }
    }

    private function genContentMT($data){
        return "Ten dang nhap cua Quy khach la: ".$data['msisdn']." Mat khau la: ".$data['password'].". Xin moi Quy khach vao trang http://weather.nhanongxanh.vn de tai ung dung va trai nghiem Dich vu MEWEATHER. Tran trong cam on!";
    }

    private function sendMtVas($input){
        $data['service'] = Constant::SEND_MT;
        $data['serviceCode'] = Constant::SERVICE_CODE;
        $data['param'] = [
            'msisdn' => $input['msisdn'],
            'content' => $this->genContentMT($input)
        ];
        $url = env('URL_CONTENT');
        $response = $this->callApiVas($data, $url);
        $res = json_decode($response, true);
        if(isset($res['statusCode']) && $res['statusCode'] == Constant::VAS_STATUS_SUCCESS){
            return true;
        }else{
            return false;
        }
    }

    private function getPackageDetail($msisdn) {
        $data['service'] = Constant::SUB_TRANSACTION_DETAILS;
        $data['serviceCode'] = Constant::SERVICE_CODE;
        $data['param'] = [
            'msisdn' => $msisdn,
            'startDate' => Carbon::now()->format('d/m/Y'),
            'endDate' => Carbon::now()->format('d/m/Y'),
            'packageCode' => null,
            'status' => null
        ];
        $url = env('URL_REPORT');
        $response = $this->callApiVas($data, $url);
        $res = json_decode($response, true);
        if(isset($res['statusCode']) && $res['statusCode'] == Constant::VAS_STATUS_SUCCESS){
            return ['response' => true, 'data' => $res['data']['subStatus']];
        }else{
            return ['response' => false, 'data' => null ];
        }
    }
    private function processDataSubTransaction($data){
        $aryPack = [];
        foreach ($data['subStatus'] as $item) {
            if($item['status'] == Constant::STATUS_ACTIVE){
                $tmp['packageCode'] = $item['packageCode'];
                $tmp['status'] = $item['status'];
                array_push($aryPack, $tmp);
            }
        }
        return $aryPack;
    }
    private function getKeySesssion() {
        return bin2hex(random_bytes(32));
    }

    private function convertStringToImg($input) {
        try{
            $folder = $_SERVER['DOCUMENT_ROOT'].'/uploads/images/';
            $image_base64 = base64_decode($input['avatar']);
            $file = uniqid() . '.png';
            $path = $folder .$file;
            $ok = file_put_contents($path, $image_base64);
            if($ok) return url().'/uploads/images/'.$file;
            else return null;
        }catch (Exception $e){
            Log::error($e->getMessage());
            return null;
        }

    }

}
