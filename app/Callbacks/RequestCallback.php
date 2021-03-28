<?php


namespace App\Callbacks;


use App\Helpers\BotHelper;
use App\Helpers\KeyboardHelper;
use App\Modules\Google\ApiClient;
use App\Modules\Telegram\Telegram;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\ServerResponse;

class RequestCallback extends Callback
{
    public const ERROR_TEXT = 'Произошла ошибка при обработке расчетки';

    private $client;

    public function __construct(Telegram $telegram, CallbackQuery $callbackQuery)
    {
        parent::__construct($telegram, $callbackQuery);

        $this->client = new ApiClient();
    }

    public function aliases(): array
    {
        return KeyboardHelper::getMonth();
    }

    public function execute(): ServerResponse
    {
        $userId = $this->callbackQuery->getFrom()->getId();
        $chatId = $this->callbackQuery->getMessage()->getChat()->getId();

        $data = !in_array($this->callbackQuery->getData(), $this->aliases()) ? null : $this->callbackQuery->getData();
        if ($text = $this->client->fetchSpreadSheet($userId, $data)) {
            return BotHelper::sendGeneralMessage($chatId, $text, $this->client->statisticUrl);
        }

        return BotHelper::sendGeneralMessage($chatId, self::ERROR_TEXT);
    }
}
