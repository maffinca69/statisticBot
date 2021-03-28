<?php


namespace App\Commands;


use App\Callbacks\RequestCallback;
use App\Helpers\BotHelper;
use App\Modules\Google\ApiClient;
use App\Modules\Telegram\UserCommand;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class StartCommand extends UserCommand
{
    private ApiClient $client;

    public function __construct(Telegram $telegram, ?Update $update = null)
    {
        parent::__construct($telegram, $update);

        $this->client = new ApiClient();
    }

    public function execute(): ServerResponse
    {
        $userId = $this->update->getMessage()->getFrom()->getId();
        $chatId = $this->update->getMessage()->getChat()->getId();
        Request::sendChatAction([
            'chat_id' => $chatId,
            'action' => ChatAction::TYPING
        ]);

        if ($text = $this->client->fetchSpreadSheet($userId)) {
            return BotHelper::sendGeneralMessage($chatId, $text, $this->client->statisticUrl);
        }

        return $this->replyToChat(RequestCallback::ERROR_TEXT);
    }
}
