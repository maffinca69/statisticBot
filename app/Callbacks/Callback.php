<?php


namespace App\Callbacks;


use App\Modules\Telegram\Telegram;
use App\Traits\AuthTrait;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use MongoDB\Driver\Server;

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

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Aliases for trigger callback
     *
     * @return array
     */
    public function aliases(): array
    {
        return [];
    }

    /**
     * @param $chatId
     * @return ServerResponse
     */
    private function sendTypingAction($chatId): ServerResponse
    {
        return Request::sendChatAction([
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
