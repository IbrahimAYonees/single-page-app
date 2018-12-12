<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormRequest;

class AuthController extends Controller
{
    public function register(RegisterFormRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->emial = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'user' => $user
        ]);
    }
}
