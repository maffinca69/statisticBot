<?php


namespace App\Traits;


use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\Message;

trait TelegramTrait
{
    /**
     * @param array $params
     * @return Message
     */
    private function prepareMessage(array $params): Message
    {
        $params = array_merge($params, [
            'chat_id' => $this->getWebhookUpdate()->getChat()->id,
            'parse_mode' => 'markdown',
        ]);

        return $this->sendMessage($params);
    }

    /**
     * @param string $text
     * @return Message|bool
     * @throws TelegramSDKException
     */
    public function replyWithMessage(string $text)
    {
        return $this->prepareMessage(['text' => $text]);
    }

    /**
     * @param string $text
     * @param Keyboard $keyboard
     * @param int|null $chatId
     * @return Message
     * @throws TelegramSDKException
     */
    public function replyWithMessageKeyboard(string $text, Keyboard $keyboard, int $chatId = null): Message
    {
        $params = [
            'text' => $text,
            'reply_markup' => $keyboard
        ];

        if (!is_null($chatId)) {
            $params['chat_id'] = $chatId;
        }

        return $this->prepareMessage($params);
    }
}
