<?php


namespace App\Commands;


use App\Keyboards\LogoutKeyboard;
use App\Modules\Telegram\Command;

class LogoutCommand extends Command
{
    public function isAuthRequired(): bool
    {
        return false;
    }


    protected $name = 'logout';

    protected $description = 'Выход из аккаунта';

    protected function execute($arguments)
    {
        $this->getTelegram()->replyWithMessageKeyboard(
            'Вы действительно хотите выйти из аккаунта?',
            $this->getTelegram()->keyboard(new LogoutKeyboard())
        );
    }
}
