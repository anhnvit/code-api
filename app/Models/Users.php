<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 3/26/2020
 * Time: 4:26 PM
 */

namespace App\Models;
use App\Models\BaseModel as Eloquent;


class Users extends Eloquent
{
    protected $table = "users";
    protected $casts = [];
    protected $hidden = ['password'];
    protected $fillable = [
        'name',
        'password',
        'msisdn',
        'session',
        'push_token',
        'email',
        'avatar',
        'address',
        'sex',
        'birthday',

    ];
}