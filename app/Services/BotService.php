<?php
namespace App\Services;

use App\Helpers\CacheHelper;
use App\Modules\Google\ApiClient;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class BotService
{
    private ApiClient $client;
    private OAuthService $authService;

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
        $chatId =  $currentUpdate->getMessage()->getChat()->getId();
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
                return Request::sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Неверная ссылка на расчетку'
                ]);
            }

            $this->authService->saveSpreadSheetId($userId, $textMatched[1]);
            return Request::sendMessage([
                'chat_id' => $chatId,
                'text' => '🎉 Синхронизация выполнена' . PHP_EOL . 'Отправьте /start, чтобы получить статистику'
            ]);
        }

        // Base functions
        if (!$update->getCallbackQuery() && $update->getMessage()->getCommand() === 'select') {
            return $this->sendSelectKeyboard($currentUpdate);
        }

        $callback = $update->getCallbackQuery() ? $update->getCallbackQuery()->getData() : null;

        if ($text = $this->client->fetchSpreadSheet($userId, $callback)) {
            return Request::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'markdown'
            ]);
        }

        return Request::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Произошла ошибка при обработке расчетки',
        ]);
    }

    public function sendSelectKeyboard(Update $update)
    {
        $chatId =  $update->getMessage()->getChat()->getId();
        $rows = [];
        $period = CarbonPeriod::create(Carbon::now()->subMonths(3), Carbon::now());

        $lastMonth = '';
        foreach ($period as $index => $date) {
            if ($date->monthName === $lastMonth) {
                continue;
            }

            $data = mb_convert_case(sprintf('%s %s', $date->monthName, $date->year), MB_CASE_TITLE);
            array_push($rows, ['text' => $data, 'callback_data' => $data]);

            $lastMonth = $date->monthName;
        }

        $rows = array_chunk(array_reverse($rows), 2);
        $inline_keyboard = new InlineKeyboard($rows[0], $rows[1]);

        return Request::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Выберите месяц со статистикой',
                'reply_markup' => $inline_keyboard
        ]);
    }
}
