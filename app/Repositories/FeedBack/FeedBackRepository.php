<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 4/22/2020
 * Time: 1:12 PM
 */

namespace App\Repositories\FeedBack;


use App\Models\FeedBack;
use App\Repositories\AbstractBaseRepository;

class FeedBackRepository extends AbstractBaseRepository implements FeedBackInterface
{
    public function __construct(FeedBack $model)
    {
        parent::__construct($model);
    }

    public function updateFeedback($input) {
        return $this->create($input);
    }
}