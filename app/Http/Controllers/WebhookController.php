<?php


namespace App\Http\Controllers;


use App\Services\BotService;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request as TelegramRequest;

class WebhookController extends Controller
{
    private BotService $botService;
    private TokenService $tokenService;

    public function __construct(Request $request, BotService $service, TokenService $tokenService)
    {
        parent::__construct($request);

        $this->botService = $service;
        $this->tokenService = $tokenService;
    }

    /**
     * Bot webhook function
     *
     * @param    Request    $request
     * @return ServerResponse|void
     */
    public function handle(Request $request)
    {
        if ($response = $this->botService->execute($this->update)) {
            return $response;
        }

        return TelegramRequest::emptyResponse();
    }

    public function setWebhook(Request $request)
    {

        $this->telegram->setWebhook(env('TELEGRAM_WEBHOOK_URL'));
        return TelegramRequest::emptyResponse();
    }
}
