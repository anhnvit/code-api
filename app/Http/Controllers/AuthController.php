<?php
/**
 * Created by PhpStorm.
 * User: anhnv
 * Date: 8/14/2019
 * Time: 4:27 PM
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function login(Request $request) {
        // Attempt login
        $credentials = $request->only("username", "password");
        if (!$token = Auth::attempt($credentials)) {
            return ["login" => "Incorrect email or password."];
        }
        return [
            "token" => [
                "access_token" => $token,
                "token_type"   => "Bearer",
                "expire"       => (int) Auth::guard()->factory()->getTTL()
            ]
        ];
    }
}