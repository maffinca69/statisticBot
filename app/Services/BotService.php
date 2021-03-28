<?php

namespace App\Services;

use App\Helpers\BotHelper;
use App\Helpers\CacheHelper;
use App\Modules\Google\ApiClient;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class BotService
{
    private ApiClient $client;
    private OAuthService $authService;

    public const SUCCESS_SYNC_TEXT = '🎉 Синхронизация выполнена'.PHP_EOL.'Отправьте /start, чтобы получить статистику';
    public const INVALID_STATISTIC_URL = 'Неверная ссылка на расчетку';
    public const ERROR_TEXT = 'Произошла ошибка при обработке расчетки';

    private Telegram $telegramInstance;

    private $sourceUpdate;

    /** @var CallbackQuery|Update */
    private $currentUpdate;

    private $chatId;
    private $userId;

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
        $this->prepareUpdate($update);

        $this->sendTypingAction();

        // Auth
        if (!CacheHelper::getAccessTokenByUserId($this->userId)) {
            return $this->authService->auth($this->userId);
        }

        // Save spreadsheet url
        if (!CacheHelper::getSpreadSheetIdByUserId($this->userId)) {
            $text = $update->getMessage()->getText();
            preg_match('/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $text, $textMatched);
            if (count($textMatched) < 2) {
                return BotHelper::sendGeneralMessage($this->chatId, self::INVALID_STATISTIC_URL);
            }

            $this->authService->saveSpreadSheetId($this->userId, $textMatched[1]);

            return BotHelper::sendGeneralMessage($this->chatId, self::SUCCESS_SYNC_TEXT);
        }

        $callback = $update->getCallbackQuery() ? $update->getCallbackQuery()->getData() : null;

        // Callback
        // todo refactoring... (maybe rewrite to class)
        if ($callback) {
            switch ($callback) {
                case 'logout':
                    if ($this->authService->logout($this->userId)) {
                        return BotHelper::sendGeneralMessage($this->chatId, '🚪 Вы успешно вышли из аккаунта');
                    }

                    return BotHelper::sendGeneralMessage($this->chatId, '🛠 Произошла ошибка');
                case 'request':
                    return $this->requestStatistic();

            }
        }

        return $this->requestStatistic($callback);
    }

    private function sendTypingAction()
    {
        Request::sendChatAction([
            'chat_id' => $this->chatId,
            'action' => ChatAction::TYPING
        ]);
    }

    /**
     * @param Update $update
     */
    private function prepareUpdate(Update $update)
    {
        $this->sourceUpdate = $update;
        $this->currentUpdate = $update->getCallbackQuery() ?? $update;
        $this->chatId = $this->currentUpdate->getMessage()->getChat()->getId();

        $this->userId = $update->getCallbackQuery() ?
            $update->getCallbackQuery()->getFrom()->getId() : // callback
            $update->getMessage()->getFrom()->getId(); // message

        $this->initializeCommands();
    }

    private function initializeCommands()
    {
        $commands_paths = [
            __DIR__ . '/../../Commands',
        ];

        $this->getTelegram()->addCommandsPaths($commands_paths);

        $this->getTelegram()->processUpdate($this->sourceUpdate);
    }

    /**
     * @param $callback
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    private function requestStatistic($callback = null): ServerResponse
    {
        if ($text = $this->client->fetchSpreadSheet($this->userId, $callback)) {
            return BotHelper::sendGeneralMessage($this->chatId, $text, $this->client->statisticUrl);
        }

        return BotHelper::sendGeneralMessage($this->chatId, self::ERROR_TEXT);
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
