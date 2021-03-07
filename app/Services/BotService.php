<?php
namespace App\Services;

use App\Modules\Google\ApiClient;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;

class BotService
{
    private const OWNER_ID = 239095324;
    public const OWNER_CHAT_ID = 239095324;

    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function execute(Update $update): ServerResponse
    {
        $currentUpdate = $update->getCallbackQuery() ?? $update;
        $chatId =  $currentUpdate->getMessage()->getChat()->getId();

        // Restrict
        if (!$update->getCallbackQuery() && $update->getMessage()->getFrom()->getId() !== self::OWNER_ID) {
            return Request::sendMessage([
                'chat_id' => $chatId,
                'text' => '👺',
            ]);
        }

        Request::sendChatAction([
            'chat_id' => $chatId,
            'action' => ChatAction::TYPING
        ]);

        if (!$update->getCallbackQuery() && $update->getMessage()->getCommand() === 'select') {
            return $this->sendSelectKeyboard($currentUpdate);
        }

        $callback = $update->getCallbackQuery() ? $update->getCallbackQuery()->getData() : null;

        if ($text = $this->client->fetchSpreadSheet($callback)) {
            return Request::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
            ]);
        }

        return Request::emptyResponse();
    }

    public function sendSelectKeyboard(Update $update)
    {
        $chatId =  $update->getMessage()->getChat()->getId();
        $rows = [];
        $rows2 = [];
        $period = CarbonPeriod::create(Carbon::now()->subMonths(3), Carbon::now());

        $lastMonth = '';
        foreach ($period as $index => $date) {
            if ($date->monthName === $lastMonth) {
                continue;
            }
            $data = mb_convert_case(sprintf('%s %s', $date->monthName, $date->year), MB_CASE_TITLE);

            if ($index % 2 === 0 && $index !== 0) {
                array_push($rows, ['text' => $data, 'callback_data' => $data]);
            } else {
                array_push($rows2, ['text' => $data, 'callback_data' => $data]);
            }

            $lastMonth = $date->monthName;
        }

        $inline_keyboard = new InlineKeyboard(array_reverse($rows), array_reverse($rows2));

        return Request::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Выберите месяц со статистикой',
                'reply_markup' => $inline_keyboard
        ]);
    }
}
