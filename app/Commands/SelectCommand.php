<?php


namespace App\Commands;


use App\Helpers\BotHelper;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class SelectCommand extends UserCommand
{

    public function execute(): ServerResponse
    {
        $chatId = $this->update->getMessage()->getChat()->getId();

        return BotHelper::sendSelectKeyboard($chatId);
    }
}
