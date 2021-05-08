<?php


namespace App\Modules\Telegram;


use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;

class Telegram extends Api
{
    /**
     * @param string $text
     * @return Message|bool
     * @throws TelegramSDKException
     */
    public function replyWithMessage(string $text)
    {
        if (!($update = $this->getWebhookUpdate())) {
            return false;
        }

        return $this->sendMessage([
            'chat_id' => $update->getChat()->id,
            'parse_mode' => 'markdown',
            'text' => $text,
        ]);
    }
}
