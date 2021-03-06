<?php


namespace App\Commands;


use App\Helpers\KeyboardHelper;
use App\Modules\Telegram\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class SelectCommand extends UserCommand
{

    public function execute(): ServerResponse
    {
        $keyboard = KeyboardHelper::inlineKeyboardSelectMonth();

        return $this->replyToChat('Выберите месяц со статистикой', [
            'reply_markup' => $keyboard
        ]);
    }
}
