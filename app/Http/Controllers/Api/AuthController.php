<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class AuthController extends Controller
{
    public function register(Request $request)
{
    $registrationData = $request->all();

    $validate = Validator::make($registrationData, [
        'name' => 'required|max:60',
        'email' => 'required|email:rfc,dns|unique:users',
        'password' => 'required|min:8|confirmed', // Laravel will automatically check for `password_confirmation`
    ]);

    if ($validate->fails()) {
        return response(['message' => $validate->errors()->first()], 400);
    }

    $registrationData['password'] = bcrypt($registrationData['password']);

    // Hanya ambil field yang dibutuhkan saja
    $user = User::create([
        'name' => $registrationData['name'],
        'email' => $registrationData['email'],
        'password' => $registrationData['password'],
    ]);

    return response([
        'message' => 'Register Success',
        'user' => $user
    ], 200);
}


    public function login(Request $request)
    {
        $loginData = $request->all();

        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required|min:8',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()->first()], 400);
        }

        if (!Auth::attempt($loginData)) {
            return response(['message' => 'Invalid email & password match'], 401);
        }
        $user = Auth::user();
        $token = $user->createToken('Authentication Token')->accessToken;

        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response([
            'message' => 'Logged out'
        ]);
    }
}
