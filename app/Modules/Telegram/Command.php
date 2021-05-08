<?php


namespace App\Modules\Telegram;


use App\Traits\AuthTrait;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

abstract class Command extends \Telegram\Bot\Commands\Command
{
    use AuthTrait;

    public function handle()
    {
        if ($this->isAuthRequired() && !$this->isAuth()) {
            Log::info('access denied');
            return;
        }

        $this->execute($this->getArguments());
    }

    /**
     * @return \App\Modules\Telegram\Api
     */
    public function getTelegram(): Api
    {
        return $this->telegram;
    }


    abstract protected function execute($arguments);
}
