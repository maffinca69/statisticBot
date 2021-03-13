<?php

namespace App\Services;

use App\Helpers\BotHelper;
use App\Helpers\CacheHelper;
use App\Modules\Google\ApiClient;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class BotService
{
    private ApiClient $client;
    private OAuthService $authService;

    public const SUCCESS_SYNC_TEXT = '🎉 Синхронизация выполнена'.PHP_EOL.'Отправьте /start, чтобы получить статистику';
    public const INVALID_STATISTIC_URL = 'Неверная ссылка на расчетку';
    public const ERROR_TEXT = 'Произошла ошибка при обработке расчетки';

    public function __construct(ApiClient $client, OAuthService $authService)
    {
        $this->client = $client;
        $this->authService = $authService;
    }

    /**
     * General bot logic function
     *
     * @param    Update    $update
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(Update $update): ServerResponse
    {
        $currentUpdate = $update->getCallbackQuery() ?? $update;
        $chatId = $currentUpdate->getMessage()->getChat()->getId();
        $userId = $update->getCallbackQuery() ?
            $update->getCallbackQuery()->getFrom()->getId() :
            $update->getMessage()->getFrom()->getId();

        Request::sendChatAction([
            'chat_id' => $chatId,
            'action' => ChatAction::TYPING
        ]);

        // Auth
        if (!CacheHelper::getAccessTokenByUserId($userId)) {
            return $this->authService->auth($userId);
        }

        // Save url
        if (!CacheHelper::getSpreadSheetIdByUserId($userId)) {
            $text = $update->getMessage()->getText();
            preg_match('/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $text, $textMatched);
            if (count($textMatched) < 2) {
                return BotHelper::sendGeneralMessage($chatId, self::INVALID_STATISTIC_URL);
            }

            $this->authService->saveSpreadSheetId($userId, $textMatched[1]);

            return BotHelper::sendGeneralMessage($chatId, self::SUCCESS_SYNC_TEXT);
        }

        // Base functions
        if (!$update->getCallbackQuery() && $update->getMessage()->getCommand() === 'select') {
            return BotHelper::sendSelectKeyboard($chatId);
        }

        $callback = $update->getCallbackQuery() ? $update->getCallbackQuery()->getData() : null;

        if ($text = $this->client->fetchSpreadSheet($userId, $callback)) {
            return BotHelper::sendGeneralMessage($chatId, $text, $this->client->statisticUrl);
        }

        return BotHelper::sendGeneralMessage($chatId, self::ERROR_TEXT);
    }
}
