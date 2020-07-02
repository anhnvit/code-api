<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 8/14/2019
 * Time: 10:01 AM
 */

namespace App\Services;


use Illuminate\Support\Facades\Log;

class BaseApi
{
    public function __construct()
    {

    }

    protected function callApiVas($data, $url){
        $userName = env('API_USERNAME');
        $password = env('API_PASSWORD');
        $auth = base64_encode($userName . ":" . $password);
        $auth = "Basic " . $auth;
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_HTTPAUTH, CURLAUTH_BASIC,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $auth,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($data)
        ));
        $message = "";
        $message .= "=============================BEGIN " . $data['serviceCode'] . "&&" . $data['service'] . " =========================\n";
        $message .= "URL : " . $url . " : Username: " . $userName . " : Password: " . $password;
        $message .= "\nDATA SEND : " . json_encode($data);
        $response = curl_exec($ch);
        $message .= "\nDATA RECEIVE : " . json_encode($response) . "\n";
        $message .= "SERVER_ERROR : " . curl_error($ch) . "\n";
        $message .= "=============================END " . $data['serviceCode'] . "=========================\n\n\n";
        Log::info(get_class() . ": " . $message);
        return $response;
    }




}