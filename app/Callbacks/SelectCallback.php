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
        return $this->telegram->executeCommand('select');
    }
}
