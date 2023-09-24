<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token');

        return $this->apiResponse([
            'token_type' => 'Bearer',
            'access_token' => $token->accessToken,
            'expires_at' => $token->token->expires_at
        ]);
    }

    /**
     * User Login.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $user = DB::table('users')
            ->where('email', $request->email)
            ->first();
        if (!$user) {
            return $this->respondUnAuthorized( 'Credentials not match');
        }
        if (!Hash::check($request->password, $user->password)) {
            return $this->respondUnAuthorized( 'Credentials not match');
        }

        auth()->loginUsingId($user->id);

        /** @var User $user */
        $user = auth()->user();

        $token = $user->createToken('auth_token');

        return $this->apiResponse([
            'token_type' => 'Bearer',
            'access_token' => $token->accessToken,
            'expires_at' => $token->token->expires_at
        ]);
    }

    /**
     * User Logout.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        /** @var User $user */
        $user = auth()->user();
        $token = $user->token();
        $token->revoke();
        return $this->apiResponse([
            'message' => 'Successfully logged out'
        ]);
    }
}
