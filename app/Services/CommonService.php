<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 3/26/2020
 * Time: 9:54 AM
 */

namespace App\Services;


class CommonService
{

    function generateRandomString($length = 4)
    {
        $characters = '0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    /**
     * Hàm chuyển đầu số 0-> 84
     * @param $phone
     * @return mixed|string
     */
    function reversephone($phone)
    {
        $phone = str_replace("+", "", $phone);
        $rest = substr($phone, 0, 1);
        if ($rest == '0') {
            $phone = "84" . substr($phone, 1);
        }
        $rest = substr($phone, 0, 2);
        if ($rest != '84') {
            $phone = "84" . $phone;
        }
        return $phone;
    }

}