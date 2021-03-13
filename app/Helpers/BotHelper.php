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
     * @return ServerResponse
     * @throws TelegramException
     */
    public static function sendGeneralMessage(int $chatId, string $text, string $statisticUrl = '')
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'markdown'
        ];

        if ($text !== ApiClient::TOKEN_IS_EXPIRED && !empty($statisticUrl)) {
            $params['reply_markup'] = KeyboardHelper::buildInlineKeyboardLinkButton($statisticUrl);
        }

        return Request::sendMessage($params);
    }

    /**
     * @param    int    $chatId
     * @return ServerResponse
     * @throws TelegramException
     */
    public static function sendSelectKeyboard(int $chatId)
    {
        $keyboard = KeyboardHelper::buildSelectMonthInlineKeyboard();

        return Request::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите месяц со статистикой',
            'reply_markup' => $keyboard
        ]);
    }

    /**
     * @param    int    $chatId
     * @return ServerResponse
     * @throws TelegramException
     */
    public static function sendConfirmLogoutMessage(int $chatId)
    {
        return Request::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Вы действительно хотите выйти из аккаунта?',
            'reply_markup' => KeyboardHelper::inlineLogoutKeyboard()
        ]);
    }
}
