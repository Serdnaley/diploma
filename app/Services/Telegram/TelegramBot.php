<?php
/**
 * Created by PhpStorm.
 * User: Andrew Pavluk (pavlukpro)
 * Date: 2020-02-03
 */

namespace App\Services\Telegram;


use App\TelegramChat;
use App\User;
use Illuminate\Http\JsonResponse;
use Telegram;
use TelegramAPI;
use Log;
use Telegram\Bot\Objects\Update;
use Storage;

class TelegramBot
{

    /**
     * Файл лога последнего обращения от Telegram
     *
     * @var string
     */
    public $log_file_name = 'telegram_update.json';


    /**
     * Полученное сообщение
     *
     * @var Telegram\Bot\Objects\Message
     */
    public $message;


    /**
     * Локальный чат телеграма
     *
     * @var TelegramChat
     */
    public $chat;


    /**
     * Команада, которую отправил пользователь боту
     *
     * @var string
     */
    public $command;


    public function webhook()
    {

//        $this->saveLocalUpdate();
        $updates = TelegramAPI::getWebhookUpdates();
//        $updates = $this->getLocalUpdates();


        if (empty($updates->getRawResponse())) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Updates not found.',
            ]);
        }

        $this->message = $updates->recentMessage();

        if (!$this->message) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Message not found.',
            ]);
        }

        $message_chat = $this->message->getChat();



        if (!$message_chat) {
            return new JsonResponse([
                'error' => true,
                'message' => 'There is no chat in the message.',
            ]);
        }

        $this->chat = $this->findTelegramChat($message_chat);

        if ( $message_chat->getType() !== 'private' ) {
            return new JsonResponse([
                'error' => true,
                'message' => 'The bot processes requests only from private chats.',
            ]);
        }

        if (!$this->validateUser()) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Требуется авторизация пользователя в системе.',
            ]);
        }

        $this->command = $this->message->getText();

        // Ссылка для входа в систему
        if ( $this->command == 'Войти в систему' ) {
            TelegramAPI::sendMessage([
                'chat_id' => $this->chat->id,
                'text' => "Используйте ссылку для входа в приложение.".
                    "\nЕсли ссылка перестала работать - откройте меню и запросите новую.",
                'parse_mode' => 'Markdown',
                'reply_markup' => TelegramAPI::replyKeyboardMarkup([
                    'inline_keyboard' => [
                        [
                            ['text' => 'Перейти в систему', 'url' => $this->chat->user->auth_url],
                        ]
                    ],
                ])
            ]);
        }
        // Все остальные команды
        else {
            TelegramAPI::sendMessage([
                'chat_id' => $this->chat->id,
                'text' => "Используйте меню для работы с системой.",
                'parse_mode' => 'Markdown',
                'reply_markup' => $this->defaultKeyboard()
            ]);
        }


        return new JsonResponse([
            'success' => true,
            'message' => 'All right.',
            'data' => $this->chat,
        ]);
    }

    /**
     * Находит чат и обновляет его данные
     *
     * @param Telegram\Bot\Objects\Chat $chat
     *
     * @return mixed
     */
    public function findTelegramChat($chat)
    {
        $telegram_chat = TelegramChat::updateOrCreate([
            'id' => $chat->getId(),
        ], [
            'id' => $chat->getId(),
            'type' => $chat->getType(),
            'title' => $chat->getTitle(),
            'first_name' => $chat->getFirstName(),
            'last_name' => $chat->getLastName(),
            'username' => $chat->getUsername(),
        ]);

        return $telegram_chat;
    }

    /**
     * Проверяем подвязку текущего юзера к системе
     *
     * @return bool
     */
    public function validateUser()
    {
        // Если юзер уже подвязан к системе
        if ($this->chat->user) {
            return true;
        }

        TelegramAPI::sendMessage([
            'chat_id' => $this->chat->id,
            'text' => "Ваш аккаунт ещё не активирован. \nОбратитесь к администрации для активации.\n\n@serdnaley",
            'parse_mode' => 'Markdown',
            'reply_markup' => $this->defaultKeyboard()
        ]);

        return false;
    }


    /**
     * Кливиатура по умолчанию
     *
     * @return string
     */
    public function defaultKeyboard()
    {
        return TelegramAPI::replyKeyboardMarkup([
            'keyboard' => [
                [
                    ['text' => 'Войти в систему'],
                ]
            ],
        ]);
    }


    /**
     * Удаляет клавиатуру
     *
     * @return string
     */
    public function removeKeyboard()
    {
        return TelegramAPI::replyKeyboardMarkup([
            'remove_keyboard' => true,
        ]);
    }


    /**
     * Сохраняет номер телефона для чата (пользователя)
     *
     * @param $phone
     *
     * @return bool
     */
    public function saveChatPhone($phone)
    {
        return $this->chat->update(['phone' => $phone]);
    }


    /**
     * Находит последнюю сохранённую команду от бота и возвращает объект Update
     *
     * @return Update
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getLocalUpdates()
    {
        $disk = Storage::disk('public');

        if ($disk->exists($this->log_file_name)) {
            $body = $disk->get($this->log_file_name);
            $body = json_decode($body, true);
        } else {
            $body = new \stdClass();
        }
        return new Update($body);
    }


    /**
     * Сохраняет последний месседж от Telegram в файл
     *
     * @param string $update
     */
    public function saveLocalUpdate($update = '')
    {
        if (!$update) {
            $update = file_get_contents('php://input');
        }

//        Log::debug($update);

        Storage::disk('public')->put($this->log_file_name, $update);
    }

}
