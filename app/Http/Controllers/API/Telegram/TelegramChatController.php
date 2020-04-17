<?php

namespace App\Http\Controllers\API\Telegram;

use App\Http\Controllers\Controller;
use App\Http\Resources\TelegramChatResource;
use App\TelegramChat;
use Illuminate\Http\Request;

class TelegramChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $chats = TelegramChat::all()->sortBy('name');

        if ($request->filter_by == 'all') {
            $groups = $chats->where('type', '!=', 'private');
            $private = $chats->where('type', '==', 'private');

            $all = $private->merge($groups);
        } elseif ($request->filter_by == 'chats') {
            $all = $chats->where('type', '!=', 'private');
        } else {
            $all = $chats->where('type', '==', 'private');
        }

        return TelegramChatResource::collection($all);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param $id
     *
     * @return TelegramChatResource
     */
    public function show($id)
    {
        $chat = TelegramChat::find($id);

        if (!$chat) {
            return response()->json([
                'error' => true,
                'message' => 'Этот объект не найдена. Возможно, он был удалён.',
            ], 404);
        }

        return new TelegramChatResource($chat);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Создает новую ссылку для вступления в чат
     *
     * @param $id
     *
     * @return TelegramChatResource|\Illuminate\Http\JsonResponse
     */
    public function inviteLink($id) {
        $chat = TelegramChat::find($id);

        if (!$chat) {
            return response()->json([
                'error' => true,
                'message' => 'Этот объект не найдена. Возможно, он был удалён.',
            ], 404);
        }

        $result = $chat->createInviteLink();

        if ( !$result ) {
            return response()->json([
                'error' => true,
                'message' => 'Ошибка создания ссылки. Убедитесь, что бот является администратором выбранного чата.'
            ], 422);
        }

        return new TelegramChatResource($chat);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
