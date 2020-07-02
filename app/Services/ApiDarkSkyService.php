<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 4/22/2020
 * Time: 4:16 PM
 */

namespace App\Services;


use Illuminate\Support\Facades\Log;

class ApiDarkSkyService
{
    public function requestApi($data)
    {
        $apiDarkSky = env('URL_DARKSKY');
        $urls = [];
        foreach ($data as $item){
            $urlApi = $apiDarkSky."/".$item["lat"].",".$item["long"];
            array_push($urls, $urlApi);
        }
        $ch = array();
        $mh = curl_multi_init();
        $i = 0;
        foreach ($urls as $url) {
            $ch[$i] = curl_init();
            curl_setopt($ch[$i], CURLOPT_URL, $url);
            curl_setopt($ch[$i], CURLOPT_HEADER, 0);
            curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($mh, $ch[$i]);
            $i++;
        }
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($active);
        $response = array();
        foreach ($ch AS $i => $c) {
            $response[$i] = json_decode(curl_multi_getcontent($c),true);
            curl_multi_remove_handle($mh, $c);
        }
        curl_multi_close($mh);
        return $response;
    }
}