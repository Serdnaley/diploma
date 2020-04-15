<?php

namespace App\Http\Controllers\API\Telegram;

use App\Http\Controllers\Controller;
use TelegramBot;
use Telegram;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    /**
     * Обработка входящих запросов от Telegram
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request) {

        return TelegramBot::webhook();
    }

    /**
     * Установка вебхука
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setWebhook() {
        $uri = route('telegram.webhook');
        $response = Telegram::setWebhook([
            'url' => $uri
        ]);

        return response()->json($response);
    }

    /**
     * Информация о боте
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBotInfo() {
        $response = Telegram::getMe();
        return response()->json($response);
    }

}
