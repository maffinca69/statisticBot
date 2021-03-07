<?php


namespace App\Http\Controllers;


use App\Services\BotService;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request as TelegramRequest;

class WebhookController extends Controller
{
    private BotService $botService;
    private TokenService $tokenService;

    public function __construct(BotService $service, TokenService $tokenService)
    {
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
        parent::handle($request);


        if ($response = $this->botService->execute($this->update)) {
            return $response;
        }

        return TelegramRequest::emptyResponse();
    }

    /**
     * Update access_token (refresh token)
     *
     * @return JsonResponse|ServerResponse
     */
    public function refresh()
    {
        $this->tokenService->refreshToken();

        if (Cache::has(TokenService::CACHE_ACCESS_TOKEN_KEY)) {
            return TelegramRequest::emptyResponse();
        }

        return response()->json(['status' => false]);
    }
}
