<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if ($token = $this->guard()->attempt($credentials)) {
            return response()->json([
                'status' => 'success',
                'user' => \Auth::user()->toArray(),
            ], 200)
                ->header('Authorization', $token);
        }

        return response()->json(['error' => 'login_error'], 422);
    }

    public function fast_auth(Request $request)
    {
        $token = $request->auth_token;

        if ($token) {
            $user = User::where(['fast_auth_token' => $token])->first();

            if ($user) {
                if ($token = $this->guard()->login($user)) {
                    return response()->json([
                        'status' => 'success',
                        'data' => $user->toArray(),
                    ], 200)
                        ->header('Authorization', $token);
                }
            }
        }

        return response([
            'error' => true,
            'error_type' => 'invalid_token',
            'message' => 'Invalid auth token.'
        ], 422);
    }

    public function logout()
    {
        $this->guard()->logout();

        return response()->json([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }

    public function user(Request $request)
    {
        $user = \Auth::user();
        return new UserResource($user);
    }


    public function refresh()
    {
        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'success'], 200)
                ->header('Authorization', $token);
        }
        return response()->json(['error' => 'refresh_token_error'], 401);
    }

    private function guard()
    {
        return \Auth::guard();
    }

}
