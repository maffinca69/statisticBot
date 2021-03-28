<?php


namespace App\Callbacks;


use App\Helpers\BotHelper;
use App\Modules\Google\ApiClient;
use App\Modules\Telegram\Telegram;
use App\Services\OAuthService;
use App\Services\TokenService;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\ServerResponse;

class LogoutCallback extends Callback
{

    private $authService;

    public function __construct(Telegram $telegram, CallbackQuery $callbackQuery)
    {
        parent::__construct($telegram, $callbackQuery);

        $apiClient = new ApiClient();
        $this->authService = new OAuthService(
            new \Google_Client(),
            new TokenService($apiClient)
        );
    }

    public function execute(): ServerResponse
    {
        $userId = $this->callbackQuery->getFrom()->getId();
        $chatId = $this->callbackQuery->getMessage()->getChat()->getId();

        if ($this->authService->logout($userId)) {
            return BotHelper::sendGeneralMessage($chatId, '🚪 Вы успешно вышли из аккаунта');
        }

        return BotHelper::sendGeneralMessage($chatId, '🛠 Произошла ошибка');
    }
}
