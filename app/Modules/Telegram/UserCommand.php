<?php


namespace App\Modules\Telegram;

use App\Traits\AuthTrait;
use Telegram\Bot\Commands\Command;

class UserCommand extends Command
{
    // todo нужно бы переписать на кастомную/дефолтную реализацию middleware
    use AuthTrait;

    public function handle()
    {
        if (!$this->isAuthRequired()) {
            return;
        }

        if ($this->isAuthRequired() && $this->isAuth()) {
            return;
        }

        return $this->replyWithMessage();
    }
}
