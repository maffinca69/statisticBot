<?php


namespace App\Http\Controllers;


use App\Services\BotService;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
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

        $this->botService->setTelegram($this->telegram);
    }

    /**
     * Bot webhook function
     *
     * @param Request $request
     * @return ServerResponse|void
     */
    public function handle(Request $request)
    {
        if ($response = $this->botService->execute($this->update)) {
            return $response;
        }

        return TelegramRequest::emptyResponse();
    }

    /**
     * Set current telegram webhook
     * For setting local/prod workspace
     *
     * @param Request $request
     * @return ServerResponse
     * @throws TelegramException
     */
    public function setWebhook(Request $request)
    {
        $this->telegram->setWebhook(env('TELEGRAM_WEBHOOK_URL'));
        return TelegramRequest::emptyResponse();
    }
}
