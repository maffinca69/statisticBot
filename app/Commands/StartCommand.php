<?php


namespace App\Commands;


use App\Helpers\CacheHelper;
use App\Keyboards\BaseAuthKeyboard;
use App\Modules\Google\ApiClient;
use App\Modules\Telegram\Command;
use App\Traits\DatesTrait;
use Telegram\Bot\Actions;

class StartCommand extends Command
{
    use DatesTrait;

    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start Command to get you started";

    public function getAliases(): array
    {
        return self::getTitleMonth();
    }

    private ApiClient $client;

    public function __construct()
    {
        $this->client = new ApiClient();
    }

    public function execute($arguments)
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $userId = $userId = $this->update->message->from->id;

        if ($text = $this->client->fetchSpreadSheet($userId)) {
            return $this->telegram->replyWithMessageKeyboard($text, $this->telegram->keyboard(new BaseAuthKeyboard(), $this->client->statisticUrl));
        }

        return $this->replyWithMessage(['text' => 'Произошла ошибка']);
    }

}
