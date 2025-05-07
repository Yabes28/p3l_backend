<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class AuthController extends Controller
{

    public function user(Request $request)
{
    return response()->json([
        'user' => $request->user()
    ], 200);
}
public function register(Request $request)
{
    $registrationData = $request->all();

    $validate = Validator::make($registrationData, [
        'name' => 'required|max:60',
        'handle' => 'required|max:20|unique:users',
        'email' => 'required|email:rfc,dns|unique:users',
        'password' => 'required|min:8',
        'no_telp' => 'required|min:10',
    ]);

    if ($validate->fails()) {
        return response(['message' => $validate->errors()->first()], 400);
    }

    $registrationData['password'] = bcrypt($registrationData['password']);
    $user = User::create($registrationData);

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

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'handle' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_telp' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return response()->json(['message' => 'Profil berhasil diperbarui', 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response([
            'message' => 'Logged out'
        ]);
    }
}
