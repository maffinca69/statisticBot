<?php


namespace App\Console\Commands;


use App\Helpers\BotHelper;
use App\Helpers\CacheHelper;
use App\Keyboards\BaseAuthKeyboard;
use App\Modules\Google\ApiClient;
use App\Modules\Telegram\Api;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;
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
     * @var Api
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
        $this->telegram = new Api(config('bot.api_key'));
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
        $ids = CacheHelper::getUsersIds();

        if (empty($ids)) {
            return true;
        }

        foreach ($ids as $id) {
            if ($text = $client->fetchSpreadSheet($id)) {
                $this->telegram->replyWithMessageKeyboard(
                    $text,
                    $this->telegram->keyboard(new BaseAuthKeyboard()),
                    $id
                );

                $this->info('Statistic was send! ' . $id);
            }
        }

        return true;
    }
}
