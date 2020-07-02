<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 8/13/2019
 * Time: 10:12 AM
 */

namespace App\Services;


use App\Repositories\Users\UserInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class DarkSkyService
{
    private $userRepository;
    private $apiDarkSky;
    public function __construct(UserInterface $user, ApiDarkSkyService $apiDarkSkyService)
    {
        $this->userRepository = $user;
        $this->apiDarkSky = $apiDarkSkyService;
    }
    public function getCurrentWeather($input)
    {
        try{
            $aryLocation[] = ['lat' => $input['lat'], 'long' => $input['long']];
            if(!isset($input['session'])){
//                $dataOutput = $this->testApi($input);
                $response = $this->getApiDarkSky($aryLocation);
                $dataOutput = $response[0] ?? [];
                return CommonConstant::responseSuccess($dataOutput, $input);
            }else {
                $objUser = $this->userRepository->checkExitSession($input['session']);
                if(!empty($objUser)){
//                    $dataOutput = $this->getApiDarkSky($input);
                    $response = $this->getApiDarkSky($aryLocation);
                    $dataOutput = $response[0] ?? [];
                    return CommonConstant::responseSuccess($dataOutput, $input);
                }else {
                    return CommonConstant::responseError($input, Constant::STATUS_ERROR_SESSION, Constant::ARRAY_MESSAGE_ERROR['session_not_exit']);
                }
            }
        }catch (Exception $e){
            return CommonConstant::responseError($input,500, $e->getMessage());
        }
    }

    public function testApi($input) {
        $result = [];
        $urlDarkSky = env('URL_DARKSKY');
        $response = file_get_contents($urlDarkSky."/".$input['lat'].",".$input['long']);
        $response = json_decode($response,true);
        $date = Carbon::createFromTimestamp($response['currently']['time'],'Asia/Ho_Chi_Minh')->format('d/m/Y');
        $result['currently'] = $this->currentWeather($response['currently']);
        $dateTemp = $this->getTempMinMax($response['daily']['data'], $date);
        $result['currently']['temperatureHigh'] = $dateTemp['temperatureHigh'];
        $result['currently']['temperatureLow'] = $dateTemp['temperatureLow'];
        $result['hourly'] = $this->getDataHour($response['hourly']['data']);
        $result['daily'] = $this->getDataDaily($response['daily']['data']);
        return $result;
    }

    /**
     * Hanm này dung để test asyn handle
     * @param $aryLocation
     * @return array
     *
     */
    public function getApiDarkSky($aryLocation) {
        $result = [];
        $responses = [];
        $t1 = microtime(true);
        $aryData = $this->apiDarkSky->requestApi($aryLocation);
        $t2 = microtime(true);
        Log::info(" time call API: " . round($t2 - $t1, 2) . " Sec");
        foreach ($aryData as $key => $response){
            $date = Carbon::createFromTimestamp($response['currently']['time'],'Asia/Ho_Chi_Minh')->format('d/m/Y');
            $result['currently'] = $this->currentWeather($response['currently']);
            $dateTemp = $this->getTempMinMax($response['daily']['data'], $date);
            $result['currently']['temperatureHigh'] = $dateTemp['temperatureHigh'];
            $result['currently']['temperatureLow'] = $dateTemp['temperatureLow'];
            $result['hourly'] = $this->getDataHour($response['hourly']['data']);
            $result['daily'] = $this->getDataDaily($response['daily']['data']);
            array_push($responses, $result);
        }
        return $responses;
    }
    public function currentWeather($data){
        return [
            'time' => Carbon::createFromTimestamp($data['time'],'Asia/Ho_Chi_Minh')->format('H:i d/m/Y'),
            'icon'  => $data['icon'],
            'summary' => $data['summary'],
            'temperature' => $this->convertTemperature($data['temperature']),
            'apparentTemperature' => $this->convertTemperature($data['apparentTemperature']),
            'precipIntensity' =>  $data['precipProbability'],
            'precipProbability' =>  $data['precipProbability'],
            'dewPoint' => round($this->convertTemperature($data['dewPoint'])),
            'humidity' => round($data['humidity'] * 100,1),
            'pressure' => round($data['pressure']),
            'windSpeed' => $this->convertWindSpeed($data['windSpeed']),
            'windGust' => $this->convertWindSpeed($data['windGust']),
            'windBearing' => $this->convertWindDirection($data['windBearing']),
            'uvIndex' => $data['uvIndex'],
            'nearestStormDistance' => $data['nearestStormDistance'] ?? null,
        ];
    }
    public function getTempMinMax($data, $date){
        $response = [];
        foreach ($data as $item){
            if($date == Carbon::createFromTimestamp($item['time'],'Asia/Ho_Chi_Minh')->format('d/m/Y')){
                $response['temperatureHigh'] = $this->convertTemperature($item['temperatureHigh']);
                $response['temperatureLow'] = $this->convertTemperature($item['temperatureLow']);
                break;
            }
        }
        return $response;
    }
    public function getDataHour($data) {
        $aryHour = [];
        foreach ($data as $item){
            $tmpHour = [
                'time' => Carbon::createFromTimestamp($item['time'],'Asia/Ho_Chi_Minh')->format('H:i d/m/Y'),
                'icon'  => $item['icon'],
                'summary' => $item['summary'],
                'temperature' => $this->convertTemperature($item['temperature']),
                'apparentTemperature' => $this->convertTemperature($item['apparentTemperature']),
                'precipIntensity' => round($item['precipIntensity'],1),
                'precipProbability' => round($item['precipProbability'] * 100,1),
                'dewPoint' => round($this->convertTemperature($item['dewPoint'])),
                'humidity' => round($item['humidity'] * 100,1),
                'pressure' => round($item['pressure']),
                'windSpeed' => $this->convertWindSpeed($item['windSpeed']),
                'windGust' => $this->convertWindSpeed($item['windGust']),
                'windBearing' => $this->convertWindDirection($item['windBearing']),
                'uvIndex' => $item['uvIndex'],
                'nearestStormDistance' => $item['nearestStormDistance'] ?? null,
            ];
            array_push($aryHour,$tmpHour);
        }
        return $aryHour;
    }

    public function getDataDaily($data){
        $aryDaily = [];
        foreach ($data as $item){
            $tmpDaily = [
                'time' => Carbon::createFromTimestamp($item['time'],'Asia/Ho_Chi_Minh')->format('d/m/Y'),
                'icon'  => $item['icon'],
                'summary' => $item['summary'],
                'precipIntensity' => round($item['precipIntensity'],1),
                'precipProbability' => round($item['precipProbability'] * 100,1),
                'dewPoint' => round($this->convertTemperature($item['dewPoint'])),
                'humidity' => round($item['humidity'] * 100,1),
                'pressure' => round($item['pressure']),
                'windSpeed' => $this->convertWindSpeed($item['windSpeed']),
                'windGust' => $this->convertWindSpeed($item['windGust']),
                'windBearing' => $this->convertWindDirection($item['windBearing']),
                'uvIndex' => $item['uvIndex'],
                'nearestStormDistance' => $item['nearestStormDistance'] ?? null,
                'temperatureHigh' => $this->convertTemperature($item['temperatureHigh']),
                'temperatureLow' => $this->convertTemperature($item['temperatureLow'])
            ];
            array_push($aryDaily,$tmpDaily);
        }
        return $aryDaily;
    }

    private function convertTemperature($temp)
    {
        return round(($temp - 32) / 1.8,1);
    }
    private function convertWindSpeed($speed)
    {
        return round(1.609344 * $speed,1);
    }

    private function convertWindDirection($direction){
        if($direction < 5){
            return "Bắc";
        }if ($direction < 15){
            return "Lặng gió";
        }if ($direction < 45){
            return "Bắc Đông Bắc";
        }if ($direction < 65){
            return "Đông Bắc";
        }if ($direction < 85){
            return "Đông Đông Bắc";
        }if ($direction < 105){
            return "Đông";
        }if ($direction < 125){
            return "Đông Đông Nam";
        }if ($direction < 145){
            return "Đông Nam";
        }if ($direction < 175){
            return  "Nam Đông Nam";
        }if ($direction < 195){
            return "Nam";
        }if ($direction < 225){
            return "Nam Tây Nam";
        }if ($direction < 245){
            return "Tây Nam";
        }if ($direction < 265){
            return "Tây Tây Nam";
        }if ($direction < 285){
            return "Tây";
        }if ($direction < 315){
            return "Tây Tây Bắc";
        }if ($direction < 335){
            return "Tây Bắc";
        }if ($direction < 355){
            return "Bắc Tây Bắc";
        }if ($direction < 265){
            return "Tây Tây Nam";
        }else {
            return "Bắc";
        }
    }

}