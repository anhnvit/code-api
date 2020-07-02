<?php

namespace App\Repositories\Users;


use App\Repositories\RepositoryInterface;

interface UserInterface extends RepositoryInterface
{
    public function login($input);
    public function signin($input);
    public function forgetPassword($input);
    public function logout($input);
    public function changePassword($input);
    public function getUserBySession($session);
    public function updateUserInfo($input);
    public function getDataByMsisdn($msisdn);
    public function checkExitSession($session);
    public function updatePassword($input);
}