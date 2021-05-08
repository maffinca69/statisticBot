<?php


namespace App\Keyboards;


use Telegram\Bot\Keyboard\Keyboard;

class BaseAuthKeyboard implements KeyboardInterface
{

    public function create($input = null): Keyboard
    {
        $keyboard = Keyboard::make()->inline();

        $rows = [
            Keyboard::inlineButton(['text' => 'Актуальная расчетка1', 'callback_data' => 'request']),
            Keyboard::inlineButton(['text' => 'Выбрать месяц', 'callback_data' => 'select'])
        ];

        if (!empty($input)) {
            array_push($rows, Keyboard::inlineButton(['text' => 'Ссылка на расчетку', 'url' => $input]));
        }

        foreach ($rows as $row) {
            $keyboard->row($row);
        }

        return $keyboard;
    }
}
