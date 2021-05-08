<?php


namespace App\Keyboards;


use App\Traits\DatesTrait;
use Telegram\Bot\Keyboard\Keyboard;

class SelectMonthKeyboard implements KeyboardInterface
{
    use DatesTrait;

    public function create($input = null): Keyboard
    {
        $months = self::getTitleMonth();
        $keyboard = Keyboard::make()->inline();

        foreach ($months as $index => $month) {
            $keyboard->row(Keyboard::inlineButton(['text' => $month, 'callback_data' => $month]));
        }

        return $keyboard;
    }
}
