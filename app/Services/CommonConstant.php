<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 8/21/2019
 * Time: 9:49 AM
 */

namespace App\Services;


trait CommonConstant
{
    public static function responseSuccess($response,$input, $message = "Thành công")
    {
        $result_data['cmd_code'] = $input['cmd_code'];
        $result_data['error_code'] = Constant::STATUS_SUCCESS;
        $result_data['error_message'] = $message;
        $result_data['result'] = $response;
        return $result_data;
    }

    public static function responseError($input, $statusCode, $message)
    {
        $result_data['cmd_code'] = $input['cmd_code'];
        $result_data['error_code'] = $statusCode;
        $result_data['error_message'] = $message;
        $result_data['result'] = [];
        return $result_data;
    }
}