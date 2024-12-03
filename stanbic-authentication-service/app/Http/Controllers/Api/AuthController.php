<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->responseWithError(false, __("Validation Error"), $errors, 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $token = JWTAuth::fromUser($user);
        $token = $this->respondWithToken($token);
        $data = compact('user', 'token');

        return $this->responseApi(true, __("Registration completed"), $data, 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->responseWithError(false, __("Validation Error"), $errors, 400);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->responseApi(false, __("Invalid credentials"), [], 401);
            }
        } catch (JWTException $e) {
            return $this->responseApi(false, __("Could not create token"), [], 500);
        }

        $data = $this->respondWithToken($token);

        return $this->responseApi(true, __("Login successful"), $data, 200);
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60 * 24
        ];
    }

    public function logout(Request $request)
    {

        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return $this->responseApi(false, __("Token not provided."), [], 400);
            }

            JWTAuth::invalidate($token);
            info('Token invalidated successfully');
            return $this->responseApi(true, __("You have successfully logged out."), [], 200);
        } catch (JWTException $e) {
            info('Logout error: ' . $e->getMessage());
            return $this->responseApi(false, __("Failed to logout, please try again."), [], 500);
        }
    }


    public function refresh(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                info('Token missing in refresh method.');
                return $this->responseApi(false, __("Token is required"), null, 401);
            }

            $newToken = JWTAuth::refresh();
            $data = $this->respondWithToken($newToken);

            return $this->responseApi(true, __("Token refreshed successfully"), $data, 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->responseApi(false, __("Token refresh failed"), ['error' => $e->getMessage()], 401);
        }
    }
}
