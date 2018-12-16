<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Guard;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormRequest;
use App\Http\Requests\LoginFormRequest;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthController extends Controller
{
    /**
     * @var Guard|\Illuminate\Contracts\Auth\Guard
     */
    protected $guard;

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->guard = Auth::guard('api');
    }

    /**
     * @param \App\Http\Requests\RegisterFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterFormRequest $request): JsonResponse
    {
        //register user
        $user = $this->create($request->all());

        //login the fresh created user
        $token = $this->guard->attempt($request->only('email','password'));

        //return user data and token to the front end
        return response()->json([
            'data' => [
                'user' => $user
            ],
            'meta' => [
                'token' => $token
            ]
        ],200);
    }

    /**
     * @param \App\Http\Requests\LoginFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginFormRequest $request): JsonResponse
    {
        try{
            if(!$token = $this->guard->attempt($request->only('email','password'))){
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

        //return user data and login token when login success
        return response()->json([
            'data' => $request->user(),
            'meta' => [
                'token' => $token
            ]
        ],200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->guard->invalidate($this->guard->getToken());
        return response()->json(null,200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json($this->guard->user(),200);
    }

    /**
     * @param array $data
     * @return \App\User
     */
    public function create(array $data): User
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->save();
        return $user;
    }
}
