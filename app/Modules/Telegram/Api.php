<?php


namespace App\Modules\Telegram;


use App\Commands\LogoutCommand;
use App\Commands\SelectCommand;
use App\Commands\StartCommand;
use App\Keyboards\KeyboardInterface;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\Message;

class Api extends \Telegram\Bot\Api
{

    protected array $customCommands = [
        SelectCommand::class,
        StartCommand::class,
        LogoutCommand::class
    ];

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

    /**
     * @param array $params
     * @return Message
     * @throws TelegramSDKException
     */
    private function prepareMessage(array $params): Message
    {
        $params = array_merge($params, [
            'chat_id' => $this->getWebhookUpdate()->getChat()->id,
            'parse_mode' => 'markdown',
        ]);

        return $this->sendMessage($params);
    }


    public function registerCommands()
    {
        try {
            $this->addCommands($this->customCommands);
        } catch (TelegramSDKException $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @param KeyboardInterface $keyboard
     * @param null $input
     * @return Keyboard
     */
    public function keyboard(KeyboardInterface $keyboard, $input = null): Keyboard
    {
        return $keyboard->create($input);
    }
}
