<?php

namespace App\Services;

use App\Helpers\CacheHelper;
use App\Modules\Telegram\Api;
use Longman\TelegramBot\Entities\ServerResponse;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;

class BotService
{

    /**
     * General bot logic function
     * @param Api $telegram
     * @return bool|Message|void
     */
    public function execute(Api $telegram)
    {
        $update = $telegram->getWebhookUpdate();

        $userId = $update->isType('callback_query') ?
            $update->callbackQuery->from->id :
            $update->message->from->id;

        // Auth
        if (!CacheHelper::getAccessTokenByUserId($userId)) {
            return $telegram->replyWithMessage('🔒 Необходимо авторизоваться2');
        }

        // Save spreadsheet url
        if (!CacheHelper::getSpreadSheetIdByUserId($userId)) {
            $text = $update->getMessage()->text;
            preg_match('/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $text, $textMatched);
            if (count($textMatched) < 2) {
                return $telegram->replyWithMessage('Неверная ссылка на расчетку');
            }

//            $this->authService->saveSpreadSheetId($userId, $textMatched[1]);

            return $telegram->replyWithMessage('🎉 Синхронизация выполнена'
                . PHP_EOL .
                'Отправьте /start, чтобы получить статистику');
        }

        return $update->hasCommand();
    }
}
