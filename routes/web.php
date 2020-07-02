<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->get('/', function () use ($router){
    return $router->app->version();
});
$router->post('login', [
    'as' => 'login', 'uses' => 'AuthController@login'
]);
//$router->group(['prefix' => 'api','middleware' => 'auth'], function () use ($router){
//    $router->post('dark-sky', [
//        'as' => 'darksky', 'uses' => 'DarkSkyController@index'
//    ]);
//});
$router->group(['prefix' => 'api'], function () use ($router){

    $router->post('weather', [
        'as' => 'weather', 'uses' => 'WeatherController@index'
    ]);

});

