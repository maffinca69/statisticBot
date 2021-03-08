<?php


namespace App\Console\Commands;


use App\Helpers\CacheHelper;
use App\Jobs\RefreshTokenJob;
use App\Services\TokenService;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;

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
        $ids = CacheHelper::getAllIdsUsersFromCache();

        if (empty($ids)) {
            return true;
        }

        foreach ($ids as $id) {
            dispatch(new RefreshTokenJob($service, $id));
            $this->info('Token has been refreshed! ID - ' . $id);
        }

        return true;
    }
}
