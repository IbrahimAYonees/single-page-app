<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormRequest;
use App\Http\Requests\LoginFormRequest;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(RegisterFormRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        $token = auth()->attempt(['email' => $request->email, 'password' => $request->password]);
        return response()->json([
            'data' => [
                'user' => $user
            ],
            'meta' => [
                'token' => $token
            ]
        ],200);
    }

    public function login(LoginFormRequest $request)
    {
        try{
            if(!$token = auth()->attempt($request->only('email','password'))){
                return response()->json([
                    'errors' => [
                        'root' => 'Could not sign you in with this data'
                    ]
                ],401);
            }

        }catch (JWTException $e){
            return response()->json([
                'errors' => [
                    'root' => 'failed'
                ]
            ],500);
        }

        return response()->json([
            'data' => $request->user(),
            'meta' => [
                'token' => $token
            ]
        ],200);
    }
}
