<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

class Controller extends BaseController
{
    /** @var Update */
    protected Update $update;

    /** @var Telegram */
    protected Telegram $telegram;

    public function __construct(Request $request)
    {
        $this->init($request->all());
    }

    /**
     * @param    array    $data
     * @throws TelegramException
     */
    private function init(array $data)
    {
        $username = config('bot.username');

        $this->telegram = new Telegram(config('bot.api_key'), $username);
        $this->telegram->useGetUpdatesWithoutDatabase();

        $this->update = new Update($data, $username);
    }
}
