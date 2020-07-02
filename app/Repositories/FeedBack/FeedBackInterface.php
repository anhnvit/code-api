<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 4/22/2020
 * Time: 1:12 PM
 */

namespace App\Repositories\FeedBack;


use App\Repositories\RepositoryInterface;

interface FeedBackInterface extends RepositoryInterface
{
    public function updateFeedback($input);
}