<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 3/24/2020
 * Time: 10:52 AM
 */

namespace App\Http\Controllers;


use App\Services\WeatherService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    private $weather;

    public function __construct( WeatherService $weatherService)
    {
        $this->weather = $weatherService;
    }
    public function index(Request $request)
    {
        $input = $request->all();
        $header = $request->header('Authorization', '');
        $res = $this->weather->callService($input, $header);
        return response()->json($res);
    }

}