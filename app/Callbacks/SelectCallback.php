<?php


namespace App\Callbacks;


use App\Helpers\KeyboardHelper;
use Longman\TelegramBot\Entities\ServerResponse;

class SelectCallback extends Callback
{

    // todo: копипаст из selectCommand. Нужно добавить поддержку вызова комманд из колбэков
    public function execute(): ServerResponse
    {
        $keyboard = KeyboardHelper::inlineKeyboardSelectMonth();

        return $this->replyToChat('Выберите месяц со статистикой', [
            'reply_markup' => $keyboard
        ]);
    }
}
