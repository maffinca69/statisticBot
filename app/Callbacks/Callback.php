<?php


namespace App\Callbacks;


use App\Modules\Telegram\Telegram;
use App\Traits\AuthTrait;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

abstract class Callback
{
    use AuthTrait;

    protected bool $enabled = true;

    protected CallbackQuery $callbackQuery;
    protected Telegram $telegram;

    public function __construct(Telegram $telegram, CallbackQuery $callbackQuery)
    {
        $this->callbackQuery = $callbackQuery;
        $this->telegram = $telegram;

        $this->sendTypingAction($callbackQuery->getMessage()->getChat()->getId());
    }

    abstract public function execute(): ServerResponse;

    public function preExecute(): ServerResponse
    {
        if (!$this->isAuthRequired()) {
            return $this->execute();
        }

        if ($this->isAuthRequired() && $this->isAuth()) {
            return $this->execute();
        }

        return Request::emptyResponse();
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function aliases(): array
    {
        return [];
    }

    private function sendTypingAction($chatId)
    {
        Request::sendChatAction([
            'chat_id' => $chatId,
            'action' => ChatAction::TYPING
        ]);
    }

    /**
     * Helper to reply to a chat directly.
     *
     * @param string $text
     * @param array $data
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function replyToChat(string $text, array $data = []): ServerResponse
    {
        return Request::sendMessage(array_merge([
            'chat_id' => $this->telegram->getChatId(),
            'text' => $text,
        ], $data));
    }
}
