<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;

class Controller extends BaseController
{
    /** @var Update */
    protected Update $update;

    /** @var Telegram */
    protected Telegram $telegram;

    /**
     * @param Request $request
     */
    protected function handle(Request $request) {
        $this->init($request->all());
    }

    private function init(array $data)
    {
        $username = config('bot.username');

        $this->telegram = new Telegram(config('bot.api_key'), $username);
        $this->telegram->useGetUpdatesWithoutDatabase();

        $this->update = new Update($data, $username);
    }
}
