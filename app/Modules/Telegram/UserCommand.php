<?php


namespace App\Modules\Telegram;

use App\Traits\AuthTrait;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class UserCommand extends \Longman\TelegramBot\Commands\UserCommand
{
    use AuthTrait;

    public function preExecute(): ServerResponse
    {
        if (!$this->isAuthRequired()) {
            return parent::preExecute();
        }

        if ($this->isAuthRequired() && $this->isAuth()) {
            return parent::preExecute();
        }

        return Request::emptyResponse();
    }


    public function execute(): ServerResponse
    {
        // ...
    }
}
