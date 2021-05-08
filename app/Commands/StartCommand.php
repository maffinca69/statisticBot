<?php


namespace App\Commands;


use App\Callbacks\RequestCallback;
use App\Keyboards\BaseAuthKeyboard;
use App\Modules\Google\ApiClient;
use App\Modules\Telegram\Command;
use Telegram\Bot\Actions;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start Command to get you started";


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
            return $this->telegram->replyWithMessageKeyboard($text, $this->telegram->keyboard(new BaseAuthKeyboard()));
        }

        return $this->replyWithMessage(['text' => RequestCallback::ERROR_TEXT]);
    }

}
