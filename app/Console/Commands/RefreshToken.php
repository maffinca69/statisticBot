<?php


namespace App\Console\Commands;


use App\Jobs\RefreshTokenJob;
use App\Modules\Google\ApiClient;
use App\Services\TokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class RefreshToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh google user token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param    TokenService    $service
     * @return mixed
     * @throws TelegramException
     */
    public function handle(TokenService $service)
    {
        $ids = $this->getUsersIds();

        if (empty($ids)) {
            return true;
        }

        foreach ($ids as $id) {
            dispatch(new RefreshTokenJob($service, $id));
            $this->info('Token has been refreshed!' . $id);
        }

        return true;

    }

    private function getUsersIds(): array
    {
        $redis = Cache::getRedis();
        $keys = $redis->keys("*spreadsheet_id*");
        $ids = [];
        foreach ($keys as $key) {
            preg_match('/spreadsheet_id_([a-zA-Z0-9-_]+)/', $key, $data);
            if (count($data) < 2) {
                continue;
            }

            array_push($ids, (int)$data[1]);
        }

        return $ids;
    }

}
