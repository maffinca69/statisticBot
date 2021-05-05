<?php


namespace App\Callbacks;


use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

class SelectCallback extends Callback
{

    /**
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $command = $this->callbackQuery->getMessage()->getCommand();
        return $this->telegram->executeCommand($command);
    }
}
