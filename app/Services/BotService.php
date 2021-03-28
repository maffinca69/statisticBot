<?php

namespace App\Services;

use App\Helpers\BotHelper;
use App\Helpers\CacheHelper;
use App\Modules\Telegram\Telegram;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class BotService
{
    private OAuthService $authService;

    public const SUCCESS_SYNC_TEXT = '🎉 Синхронизация выполнена' . PHP_EOL . 'Отправьте /start, чтобы получить статистику';
    public const INVALID_STATISTIC_URL = 'Неверная ссылка на расчетку';

    private Telegram $telegramInstance;

    public function __construct(OAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * General bot logic function
     *
     * @param Update $update
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(Update $update): ServerResponse
    {
        $this->initializeCommands();
        $this->getTelegram()->processUpdate($update);
        $this->getTelegram()->initializeCallbacks();

        $chatId = $this->telegramInstance->getChatId();
        $userId = $this->telegramInstance->getUserId();

        // Auth
        if (!CacheHelper::getAccessTokenByUserId($userId)) {
            return $this->authService->auth($userId);
        }

        // Save spreadsheet url
        if (!CacheHelper::getSpreadSheetIdByUserId($userId)) {
            $text = $update->getMessage()->getText();
            preg_match('/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $text, $textMatched);
            if (count($textMatched) < 2) {
                return BotHelper::sendGeneralMessage($chatId, self::INVALID_STATISTIC_URL);
            }

            $this->authService->saveSpreadSheetId($userId, $textMatched[1]);

            return BotHelper::sendGeneralMessage($chatId, self::SUCCESS_SYNC_TEXT);
        }

        // Disable command reaction
        if ($update->getUpdateType() !== 'callback_query' && $update->getMessage()->getCommand()) {
            return Request::emptyResponse();
        }

        // text message
        return Request::emptyResponse();
    }

    private function initializeCommands()
    {
        $commands_paths = [
            __DIR__ . '/../Commands',
        ];

        $this->getTelegram()->addCommandsPaths($commands_paths);
    }

    public function setTelegram(Telegram $telegram)
    {
        $this->telegramInstance = $telegram;
    }

    public function getTelegram(): Telegram
    {
        return $this->telegramInstance;
    }
}
