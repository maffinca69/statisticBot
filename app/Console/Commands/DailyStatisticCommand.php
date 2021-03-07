<?php


namespace App\Console\Commands;


use App\Modules\Google\ApiClient;
use App\Services\BotService;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class DailyStatisticCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily statistic';

    /**
     * @var Telegram
     */
    protected $telegram;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $username = config('bot.username');

        $this->telegram = new Telegram(config('bot.api_key'), $username);
        $this->telegram->useGetUpdatesWithoutDatabase();
    }

    /**
     * Execute the console command.
     *
     * @param    ApiClient    $client
     * @return mixed
     * @throws TelegramException
     */
    public function handle(ApiClient $client)
    {
        if ($text = $client->fetchSpreadSheet()) {
            $send = Request::sendMessage(
                [
                    'chat_id' => BotService::OWNER_CHAT_ID,
                    'text' => $text,
                ]
            );

            $this->info('Statistic was send!');

            return (bool) $send;
        }

        return false;
    }
}
