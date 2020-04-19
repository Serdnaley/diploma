<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Report;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::all();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UpdateUserRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
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

        $password = $request->password ?: \Str::random(8);

        $user->password = bcrypt($password);
        $user->save();

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'error',
                'message' => 'Объект не найден. Возможно он был удалён.'
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
                    'message' => 'Объект не найден. Возможно он был удалён.'
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'error',
                'message' => 'Объект не найден. Возможно он был удалён.'
            ], 404);
        }

        $user->update($request->only([
            'first_name',
            'last_name',
            'patronymic',
            'user_category_id',
            'telegram_chat_id',
            'role',
            'email',
        ]));

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
