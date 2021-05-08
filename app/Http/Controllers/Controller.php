<?php

namespace App\Http\Controllers;

use App\Modules\Telegram\Api;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;
use Telegram\Bot\Exceptions\TelegramSDKException;

class Controller extends BaseController
{
    /** @var Api */
    protected Api $api;

    public function __construct()
    {
        try {
            $this->api = new Api(config('bot.api_key'));
            $this->api->registerCommands();
            $this->api->commandsHandler(true);
        } catch (TelegramSDKException $e) {
            Log::error($e->getMessage());
        }
    }
}
