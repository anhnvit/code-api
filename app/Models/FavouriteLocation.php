<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 4/21/2020
 * Time: 4:22 PM
 */

namespace App\Models;
use App\Models\BaseModel as Eloquent;

class FavouriteLocation extends Eloquent
{
    protected $casts = [
        'user_id'
    ];
    protected $fillable = [
        'name',
        'user_id',
        'lat',
        'long',

    ];
}