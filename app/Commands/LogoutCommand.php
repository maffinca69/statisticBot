<?php


namespace App\Commands;


use App\Helpers\KeyboardHelper;
use App\Modules\Telegram\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class LogoutCommand extends UserCommand
{

    public function execute(): ServerResponse
    {
        return $this->replyToChat('Вы действительно хотите выйти из аккаунта?', [
            'reply_markup' => KeyboardHelper::inlineLogoutKeyboard()
        ]);
    }
}
