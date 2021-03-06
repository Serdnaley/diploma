<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Report;
use App\User;
use Illuminate\Http\Request;
use TelegramAPI;
use TelegramBot;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $users = User::orderByDesc('created_at')->get();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UpdateUserRequest $request
     *
     * @return UserResource
     */
    public function store(UpdateUserRequest $request)
    {
        $user = new User($request->only([
            'first_name',
            'last_name',
            'patronymic',
            'user_category_id',
            'telegram_chat_id',
            'role',
            'email',
        ]));

        $password = $request->password ?: 'password';

        $user->password = bcrypt($password);
        $user->fast_auth_token = \Str::random(10);
        $user->save();

        if ($user->telegram_chat_id) {
            TelegramAPI::sendMessage([
                'chat_id' => $user->telegram_chat_id,
                'text' => "{$user->first_name}, ваш аккаунт був активований.",
                'parse_mode' => 'Markdown',
                'reply_markup' => TelegramBot::defaultKeyboard(),
            ]);
        }

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     *
     * @return UserResource
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'error',
                'message' => 'Об\'єкт не знайдено. Можливо він був видалений.'
            ], 404);
        }

        return new UserResource($user);
    }

    /**
     * Display the reports of user.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reports($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()
                ->json([
                    'error' => 'error',
                    'message' => 'Об\'єкт не знайдено. Можливо він був видалений.'
                ], 404);
        }

        $reports = Report::whereUserId($user->id)->get();

        return response()
            ->json([
                'data' => $reports,
            ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param                   $id
     *
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'error',
                'message' => 'Об\'єкт не знайдено. Можливо він був видалений.'
            ], 404);
        }

        $data = $request->validated();

        if (isset($data['password'])) {
            if ($data['password']) {
                $data['password'] = \Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
        }

        $user->update($data);

        if ($user->wasChanged('telegram_chat_id') && $user->telegram_chat_id) {
            TelegramAPI::sendMessage([
                'chat_id' => $user->telegram_chat_id,
                'text' => "{$user->first_name}, ваш аккаунт було активовано.",
                'parse_mode' => 'Markdown',
                'reply_markup' => TelegramBot::defaultKeyboard(),
            ]);
        }

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'error',
                'message' => 'Объект не найден. Возможно он был удалён.'
            ], 404);
        }

        $user->delete();

        return response()->json(['success' => 'success']);
    }
}
