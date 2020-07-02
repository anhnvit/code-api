<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 4/22/2020
 * Time: 11:02 AM
 */

namespace App\Services;


use App\Repositories\FeedBack\FeedBackInterface;
use App\Repositories\Users\UserInterface;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class FeedBackService
{
    private $userRepository;
    private $feedbackRepository;
    public function __construct(UserInterface $user, FeedBackInterface $feedBack)
    {
        $this->userRepository = $user;
        $this->feedbackRepository = $feedBack;
    }

    public function updateFeedback($input){
        try{
            $objUser = $this->userRepository->checkExitSession($input['session']);
            if(!empty($objUser)){
                $input['msisdn'] = $objUser['msisdn'];
                $this->feedbackRepository->updateFeedback($input);
                return CommonConstant::responseSuccess([], $input);
            }else {
                return CommonConstant::responseError($input, Constant::STATUS_ERROR_SESSION, Constant::ARRAY_MESSAGE_ERROR['session_not_exit']);
            }
        }catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonConstant::responseError($input, Constant::STATUS_ERROR_SERVER,Constant::ARRAY_MESSAGE_ERROR['error']);
        }
    }
}