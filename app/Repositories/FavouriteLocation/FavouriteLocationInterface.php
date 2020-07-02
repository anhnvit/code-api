<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 4/21/2020
 * Time: 4:21 PM
 */

namespace App\Repositories\FavouriteLocation;


use App\Repositories\RepositoryInterface;

interface FavouriteLocationInterface extends RepositoryInterface
{
    public function createLocation($dataInput);

    public function getListFavouriteLocationByUser($userId);

    public function deleteFavouriteLocationById($id);
}