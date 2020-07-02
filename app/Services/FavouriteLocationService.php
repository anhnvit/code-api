<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 4/21/2020
 * Time: 4:59 PM
 */

namespace App\Services;


use App\Repositories\FavouriteLocation\FavouriteLocationInterface;
use App\Repositories\Users\UserInterface;
use Mockery\Exception;

class FavouriteLocationService
{
    private $darkSkyService;
    private $locationRepository;
    private $userRepository;

    public function __construct(DarkSkyService $darkSkyService, FavouriteLocationInterface $locationRepository, UserInterface $user)
    {
        $this->darkSkyService = $darkSkyService;
        $this->locationRepository = $locationRepository;
        $this->userRepository = $user;
    }

    public function addFavouriteLocation($input){
        $dataOutput = [];
        try{
            $objUser = $this->userRepository->checkExitSession($input['session']);
            if(!empty($objUser)){
                $input['user_id'] = $objUser['id'];
                $response = $this->locationRepository->createLocation($input);
                $aryLocation[] = ['lat' => $input['lat'], 'long' => $input['long']];
                $res = $this->darkSkyService->getApiDarkSky($aryLocation);
                $dataOutput['info'] = $response;
                $dataOutput['weather'] = $res[0] ?? [];
                return CommonConstant::responseSuccess([$dataOutput], $input);
            }else {
                return CommonConstant::responseError($input, Constant::STATUS_ERROR_SESSION, Constant::ARRAY_MESSAGE_ERROR['session_not_exit']);
            }
        }catch (Exception $e){
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR['error']);
        }

    }

    public function getListFavouriteLocation($input) {
        try{
            $objUser = $this->userRepository->checkExitSession($input['session']);
            if(!empty($objUser)){
                $dataOutput = [];
                $aryLocation = $this->locationRepository->getListFavouriteLocationByUser($objUser['id']);
                $listLocation = $this->getAryLocation($aryLocation);
                $res = $this->darkSkyService->getApiDarkSky($listLocation);
                foreach ($aryLocation as $key => $row){
                    $tmp['info'] = $row;
                    $tmp['weather'] = $res[$key];
                    array_push($dataOutput, $tmp);
                }
                return CommonConstant::responseSuccess($dataOutput, $input);
            }else {
                return CommonConstant::responseError($input, Constant::STATUS_ERROR_SESSION, Constant::ARRAY_MESSAGE_ERROR['session_not_exit']);
            }
        }catch (Exception $e){
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR['error']);
        }
    }

    public function deleteFavouriteLocation($input) {
        try{
            $objUser = $this->userRepository->checkExitSession($input['session']);
            if(!empty($objUser)){
                $this->locationRepository->deleteFavouriteLocationById($input['id']);
                return CommonConstant::responseSuccess([], $input);
            }else {
                return CommonConstant::responseError($input, Constant::STATUS_ERROR_SESSION, Constant::ARRAY_MESSAGE_ERROR['session_not_exit']);
            }
        }catch (Exception $e){
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR['error']);
        }
    }

    private function getAryLocation($data) {
        $location = [];
        $tmp = [];
        foreach ($data as $item){
            $tmp['lat'] = $item['lat'];
            $tmp['long'] = $item['long'];
            array_push($location, $tmp);
        }
        return $location;
    }
}