<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function responseSuccess($data)
    {
        return [
            'ERR_CODE' => 404,
            'ERR_MSG' => "CMD_CODE không tồn tại",
            'RESULT' => null
        ];
    }
}
