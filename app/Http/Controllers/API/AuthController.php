<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email|required|unique:users',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
            'password' => 'required'
        ]);

        $validatedData = $request->only(['first_name', 'last_name', 'email', 'phone', 'password']);
        $validatedData['password'] = bcrypt($request->password);
        // new register user cannot be admin
        $validatedData['is_admin'] = 0;

        $user = User::create($validatedData);
        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user' => new UserResource($user), 'access_token' => $accessToken],Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        // Auth::attempt wont work for api guard
        // get the first record matched by email and do hash check password
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $accessToken = $user->createToken('authToken')->accessToken;
                return response(['user' => new UserResource($user), 'access_token' => $accessToken],Response::HTTP_OK);
            }
        }

        return response(['message' => 'Invalid Credentials'],Response::HTTP_UNAUTHORIZED);
    }
}
