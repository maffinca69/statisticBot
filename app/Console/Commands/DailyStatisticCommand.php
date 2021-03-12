<?php


namespace App\Console\Commands;


use App\Helpers\CacheHelper;
use App\Modules\Google\ApiClient;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Manually send statistic
 *
 * Class DailyStatisticCommand
 * @package App\Console\Commands
 */
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
        $ids = CacheHelper::getAllIdsUsersFromCache();

        if (empty($ids)) {
            return true;
        }

        foreach ($ids as $id) {
            if ($text = $client->fetchSpreadSheet($id)) {
                Request::sendMessage([
                    'chat_id' => $id,
                    'text' => $text,
                    'parse_mode' => 'markdown'
                ]);

                $this->info('Statistic was send! ' . $id);
            }
        }

        return true;
    }
}
