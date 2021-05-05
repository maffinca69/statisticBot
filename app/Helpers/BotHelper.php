<?php


namespace App\Helpers;


use App\Modules\Google\ApiClient;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class BotHelper
{
    /**
     * @param    int    $chatId
     * @param    string    $text
     * @param    string    $statisticUrl
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public static function sendBaseMessage(int $chatId, string $text, string $statisticUrl = ''): ServerResponse
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'markdown'
        ];

        if ($text !== ApiClient::TOKEN_IS_EXPIRED && !empty($statisticUrl)) {
            $params['reply_markup'] = KeyboardHelper::inlineKeyboardLinkButton($statisticUrl);
        }

        return Request::sendMessage($params);
    }

}
