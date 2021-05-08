<?php


namespace App\Commands;


use App\Keyboards\SelectMonthKeyboard;
use App\Modules\Telegram\Command;

class SelectCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "select";

    /**
     * @var string Command Description
     */
    protected $description = "Выбрать месяц со статистикой";

    protected function execute($arguments)
    {
//        $this->replyWithMessage(['text' => 213]);
        $this->getTelegram()->replyWithMessageKeyboard(
            'Выберите месяц со статистикой',
            $this->getTelegram()->keyboard(new SelectMonthKeyboard())
        );
    }
}
