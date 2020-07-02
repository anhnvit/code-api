<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 3/24/2020
 * Time: 10:53 AM
 */

namespace App\Services;


class WeatherService
{
    private $userService;
    private $darkSkyApi;
    private $locationService;
    private $feedBack;
    public function __construct(
        UserService $userService,
        DarkSkyService $darkSkyService,
        FavouriteLocationService $locationService,
        FeedBackService $feedBackService
    )
    {
        $this->userService = $userService;
        $this->darkSkyApi = $darkSkyService;
        $this->locationService = $locationService;
        $this->feedBack = $feedBackService;
    }

    public function callService($input, $header)
    {
        switch($input['cmd_code']){
            case 'get_password':
                if($this->checkAuth()){
                    return $this->userService->getPassword($input);
                }else {
                    return [
                            'cmd_code' => $input['cmd_code'],
                            'error_code' => Constant::STATUS_ERROR_AUTH,
                            'error_message' => Constant::ARRAY_MESSAGE_ERROR['authenticate'],
                            'result' => []
                            ];
                }
                break;
            case 'login':
                return $this->userService->login($input);
                break;
            case 'signin':
                return $this->userService->signin($input);
                break;
            case 'forgetPassword':
                return $this->userService->forgetPassword($input);
                break;
            case 'logout':
                return $this->userService->logout($input);
                break;
            case 'changePassword':
                return $this->userService->changePassword($input);
                break;
            case 'getUserInfo':
                return $this->userService->getUserInfo($input);
                break;
            case 'updateUserInfo':
                return $this->userService->updateUserInfo($input);
                break;
            case 'checkPackageStatus':
                return $this->userService->checkPackageStatus($input);
                break;
            case 'getPackageInfo':
                return $this->userService->getPackageInfo($input);
                break;
//            case 'getNotification':
//                return $this->userService->getPackageInfo($input);
//                break;
//            case 'sendReadStatusNotification':
//                return $this->userService->getPackageInfo($input);
//                break;
//            case 'deleteNotification':
//                return $this->userService->getPackageInfo($input);
//                break;
            case 'getCurrentWeather':
                return $this->darkSkyApi->getCurrentWeather($input);
                break;
            case 'addFavouriteLocation':
                return $this->locationService->addFavouriteLocation($input);
                break;
            case 'getListFavouriteLocation':
                return $this->locationService->getListFavouriteLocation($input);
                break;
            case 'deleteFavouriteLocation':
                return $this->locationService->deleteFavouriteLocation($input);
                break;
            case 'updateFeedback':
                return $this->feedBack->updateFeedback($input);
                break;
            default:
                return [
                    'error_code' => 404,
                    'error_message' => "Service không tồn tại",
                    'result' => null
                ];
                break;
        }
    }

    private function checkAuth(){
            $userName = env('API_USERNAME');
            $password = env('API_PASSWORD');
            $has_supplied_credentials = (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']));
            $is_authenticated = (
                $has_supplied_credentials && (
                $_SERVER['PHP_AUTH_USER'] == $userName &&
                $_SERVER['PHP_AUTH_PW'] == $password));
            return $is_authenticated;
    }
}