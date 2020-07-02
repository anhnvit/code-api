<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 4/21/2020
 * Time: 4:21 PM
 */

namespace App\Repositories\FavouriteLocation;


use App\Models\FavouriteLocation;
use App\Repositories\AbstractBaseRepository;

class FavouriteLocationRepository extends AbstractBaseRepository implements FavouriteLocationInterface
{
    public function __construct(FavouriteLocation $model)
    {
        parent::__construct($model);
    }


    public function createLocation($dataInput) {
        return $this->create($dataInput);
    }

    public function getListFavouriteLocationByUser($userId) {
        return $this->findManyBy('user_id', $userId);
    }

    public function deleteFavouriteLocationById($id) {
        return $this->deleteOneById($id);
    }
}