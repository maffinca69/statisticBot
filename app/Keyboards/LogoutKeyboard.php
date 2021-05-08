<?php


namespace App\Keyboards;


use Telegram\Bot\Keyboard\Keyboard;

class LogoutKeyboard implements KeyboardInterface
{

    public function create($input = null): Keyboard
    {
        return Keyboard::make()->inline()->row(
            Keyboard::inlineButton(['text' => 'Подтвердить', 'callback_data' => 'logout'])
        );

    }
}
