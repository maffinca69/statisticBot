<?php

namespace App\Services;

use App\Helpers\CacheHelper;
use App\Keyboards\AuthKeyboard;
use App\Modules\Telegram\Api;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

class BotService
{

    private OAuthService $oauthService;

    public function __construct(OAuthService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    /**
     * General bot logic function
     * @param Api $telegram
     * @return bool|Message|void
     * @throws TelegramSDKException
     */
    public function execute(Api $telegram)
    {
        $update = $telegram->getWebhookUpdate();

        $userId = $update->isType('callback_query') ?
            $update->callbackQuery->from->id :
            $update->message->from->id;

        // Auth
        if (!CacheHelper::getAccessTokenByUserId($userId)) {
            $url = $this->oauthService->auth($userId);
            return $telegram->replyWithMessageKeyboard(
                '🔒 Необходимо авторизоваться',
                $telegram->keyboard(new AuthKeyboard(), $url)
            );
        }

        // Save spreadsheet url
        if (!CacheHelper::getSpreadSheetIdByUserId($userId)) {
            $text = $update->getMessage()->text;
            preg_match('/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $text, $textMatched);
            if (count($textMatched) < 2) {
                return $telegram->replyWithMessage('Неверная ссылка на расчетку');
            }

            $this->oauthService->saveSpreadSheetId($userId, $textMatched[1]);

            return $telegram->replyWithMessage('🎉 Синхронизация выполнена'
                . PHP_EOL .
                'Отправьте /start, чтобы получить статистику');
        }

        // require refactoring
        if ($update->isType('callback_query')) {
            $this->handleCallback($update, $telegram);
            return;
        }

        return $update->hasCommand();
    }

    /**
     * @param Update $update
     * @param Api $telegram
     * @throws TelegramSDKException
     */
    private function handleCallback(Update $update, Api $telegram)
    {
        Log::info('Trigger callback');
        $data = $update->callbackQuery->data;
        Log::info($data);
        switch ($data) {
            case 'request':
                $telegram->triggerCommand('start', $update);
                break;
            case 'select':
                $telegram->triggerCommand('select', $update);
                break;
            case 'logout':
                $this->logout($telegram, $update->callbackQuery->from->id);
                break;
            default: // select month
                $telegram->triggerCommand($data, $update);
        }
    }

    private function logout(Api $telegram, int $id)
    {
        $logout = $this->oauthService->logout($id);
        if ($logout) {
            $telegram->replyWithMessage('Успешно вышли!');
        }
    }
}
