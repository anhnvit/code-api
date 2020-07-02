<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 3/24/2020
 * Time: 3:22 PM
 */

namespace App\Repositories\Users;


use App\Models\Users;
use App\Repositories\AbstractBaseRepository;
use App\Services\Constant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserRepository extends AbstractBaseRepository implements UserInterface
{
    public function __construct(Users $model)
    {
        parent::__construct($model);
    }

    public function login($input){
        unset($input['cmd_code']);
        return Users::updateOrCreate(['msisdn' => $input['msisdn']], $input);
    }

    public function signin($input) {
        return $this->create($input);
    }

    public function forgetPassword($input) {
        unset($input['cmd_code']);
        return Users::query()->where('msisdn', $input['msisdn'])->update($input);
    }

    public function logout($input) {
        return Users::query()->where('session', $input['session'])
                ->update(['push_token' => null]);
    }

    public function changePassword($input) {
        return Users::query()->where('session', $input['session'])
                ->where('password', $input['old_password'])
                ->update(['password' => $input['new_password']]);
    }

    public function getUserBySession($session){
        return Users::query()->select(
            'session',
            DB::raw('id as user_id'),
            'name',
            'msisdn',
            'email',
            'avatar',
            'address',
            'sex',
            DB::raw('DATE_FORMAT(birthday, "%d/%m/%Y") as birthday')
        )
        ->where('session', $session)->first();
    }

    public function updateUserInfo($input) {
        unset($input['cmd_code']);
        if(isset($input['birthday'])){
            $input['birthday'] = Carbon::createFromFormat('d/m/Y', $input['birthday'])->format('Y-m-d');
        }
        $result = Users::query()->where('session', $input['session'])
                ->update($input);
        if($result) {
            return $this->findOneByCredentials(['session' => $input['session']]);
        } else {
            return false;
        }
    }
    public function getDataByMsisdn($msisdn){
        return $this->findOneByCredentials(['msisdn' => $msisdn]);
    }

    public function checkExitSession($session) {
        return $this->findOneByCredentials(['session' => $session]);
    }

    public function updatePassword($input){
        return Users::query()->where('msisdn', $input['msisdn'])->update($input);
    }

}