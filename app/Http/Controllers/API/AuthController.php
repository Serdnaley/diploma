<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
//    public function register(Request $request)
//    {
//        $v = \Validator::make($request->all(), [
//            'login' => 'required|alpha_dash|unique:users|min:6',
//            'email' => 'required|email|unique:users',
//            'password' => 'required|min:3|confirmed',
//            'country' => ['required', 'string', new CountryKeyRule()],
//            'agent_id' => 'nullable|alpha_dash',
//        ]);
//
//        if ($v->fails()) {
//            return response()->json([
//                'status' => 'error',
//                'errors' => $v->errors()
//            ], 422);
//        }
//
//        $user_data = $request->only([
//            'login',
//            'email',
//            'first_name',
//            'last_name',
//            'country',
//            'telegram',
//            'whatsapp',
//            'line',
//            'skype',
//            'wechat',
//            'viber',
//        ]);
//
//        $user = new User($user_data);
//        $user->agent_id = Str::random(8);
//        $user->password = bcrypt($request->password);
//
//        if ($request->agent_id) {
//            $parent_user = User::whereAgentId($request->agent_id)->first();
//            if ($parent_user) {
//                $user->parent_id = $parent_user->id;
//            }
//        }
//
//        $user->save();
//
//        return response()->json(['status' => 'success'], 200);
//    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if ($token = $this->guard()->attempt($credentials)) {
            return response()->json(['status' => 'success'], 200)->header('Authorization', $token);
        }

        return response()->json(['error' => 'login_error'], 422);
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
        $user = User::find(\Auth::user()->id);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
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
