<?php


namespace App\Http\Controllers;


use App\Services\BotService;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;

class WebhookController extends Controller
{
    private BotService $botService;
    private TokenService $tokenService;

    public function __construct(BotService $service, TokenService $tokenService)
    {
        parent::__construct();

        $this->botService = $service;
        $this->tokenService = $tokenService;
    }

    /**
     * Bot webhook function
     *
     * @param Request $request
     * @return bool|ServerResponse|Message
     * @throws TelegramSDKException
     */
    public function handle(Request $request)
    {
        if ($response = $this->botService->execute($this->api)) {
            return $response;
        }
    }

    /**
     * Set current telegram webhook
     * For setting local/prod workspace
     *
     * @param Request $request
     * @return string
     */
    public function setWebhook(Request $request)
    {
        $this->api->setWebhook(['url' => env('TELEGRAM_WEBHOOK_URL')]);
        return 'ok';
    }
}
