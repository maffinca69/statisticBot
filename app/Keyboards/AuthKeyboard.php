<?php


namespace App\Keyboards;


use Telegram\Bot\Keyboard\Keyboard;

class AuthKeyboard implements KeyboardInterface
{
    public function create($input = null): Keyboard
    {
        return Keyboard::make()->inline()->row(Keyboard::inlineButton([
            'text' => 'Войти',
            'url' => $input
        ]));
    }
}
