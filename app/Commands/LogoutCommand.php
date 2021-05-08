<?php


namespace App\Commands;


use App\Helpers\KeyboardHelper;
use App\Keyboards\LogoutKeyboard;
use App\Modules\Telegram\Command;
use App\Modules\Telegram\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class LogoutCommand extends Command
{

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
