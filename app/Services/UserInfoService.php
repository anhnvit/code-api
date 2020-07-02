<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 8/22/2019
 * Time: 1:31 PM
 */

namespace App\Services;


use App\Models\UserInfo;
use Illuminate\Support\Facades\DB;

class UserInfoService extends BaseApi
{
    private $cms_service;

    public function __construct(ContentSubService $cmsService)
    {
        $this->cms_service = $cmsService;
    }

    public function updateUserInfo($input)
    {
        $data['CMD_CODE'] = $input['CMD_CODE'];
        unset($input['CMD_CODE']);
        $input['packageId'] = str_replace(" ","",$input['packageId']);
        $res = UserInfo::updateOrCreate(['deviceId' => $input['deviceId']],$input);
        if($res){
            return $this->responseSuccess([],$data);
        }else{
            return $this->responseError($data);
        }
    }

    public function updateFavouritePackage($input)
    {
        $data = UserInfo::query()->where('deviceId', $input['deviceId'])->get();
        if(!empty($data)){
            $arrPackageId = explode(",",$data[0]['packageId']);
            if (($key = array_search($input['packageId'], $arrPackageId)) !== false) {
                unset($arrPackageId[$key]);
                $listPackageId = implode(",",$arrPackageId);
                $result = UserInfo::where('deviceId', $input['deviceId'])->update(['packageId' => $listPackageId]);
                if($result) return $this->response([], $input);
                else return $this->response(500,$input);
            }else{
                $packageId = $data[0]['packageId'].",".$input['packageId'];
                $result = UserInfo::where('deviceId',$input['deviceId'])->update(['packageId' => $packageId]);
                if($result) return $this->response([], $input);
                else return $this->response(500,$input);
            }
        }
        return $this->responseSuccess([],$input);
    }

    public function getFavouritePackageContentByUser($input)
    {
        $data = UserInfo::query()->where('deviceId', $input['deviceId'])->get();
        if(sizeof($data) > 0){
            $input['packageId'] = $data[0]['packageId'];
            return $this->cms_service->getFavouritePackageContentByUser($input);
        }else{
            return $this->responseSuccess([],$input);
        }
    }


}