<?php
/**
 * Created by PhpStorm.
 * User: Andrew Pavluk (pavlukpro)
 * Date: 2020-02-16
 */

namespace App\Services\Telegram;


use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;

class TelegramAPI extends Api
{

    public function __construct()
    {
        parent::__construct(
            config('telegram.bot_token'),
            config('telegram.async_requests'),
            config('telegram.http_client_handler')
        );
    }

    /**
     * Send Gallary.
     *
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'photo'               => '',
     *   'caption'             => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#sendphoto
     *
     * @param array    $params
     *
     * @var int|string $params ['chat_id']
     * @var string     $params ['photo']
     * @var string     $params ['caption']
     * @var int        $params ['reply_to_message_id']
     * @var string     $params ['reply_markup']
     *
     * @return Message
     */
    public function sendMediaGroup(array $params)
    {
        $response = $this->post('sendMediaGroup', $params);

        return new Message($response->getDecodedBody());
    }

    public function deleteMessage(array $params)
    {
        $response = $this->post('deleteMessage', $params);

        return new Message($response->getDecodedBody());
    }

    public function exportChatInviteLink(array $params)
    {
        $response = $this->post('exportChatInviteLink', $params);

        return $response->getDecodedBody();
    }

}
