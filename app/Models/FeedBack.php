<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 4/22/2020
 * Time: 1:14 PM
 */

namespace App\Models;
use App\Models\BaseModel as Eloquent;


class FeedBack extends Eloquent
{
    protected $fillable = [
        'msisdn',
        'feedback'
    ];
}