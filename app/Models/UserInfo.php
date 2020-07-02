<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 8/21/2019
 * Time: 2:29 PM
 */

namespace App\Models;

use App\Models\BaseModel as Eloquent;

class UserInfo extends Eloquent
{
    protected $table = 'users_info';
    public $timestamps = false;
    protected $fillable = [
        'lat',
        'long',
        'packageId',
        'deviceId'
    ];
}