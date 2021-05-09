<?php


namespace App\Modules\Telegram;


use App\Commands\LogoutCommand;
use App\Commands\SelectCommand;
use App\Commands\StartCommand;
use App\Keyboards\KeyboardInterface;
use App\Traits\TelegramTrait;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;

class Api extends \Telegram\Bot\Api
{

    use TelegramTrait;

    protected array $customCommands = [
        SelectCommand::class,
        StartCommand::class,
        LogoutCommand::class
    ];

    public function registerCommands()
    {
        try {
            $this->addCommands($this->customCommands);
        } catch (TelegramSDKException $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @param KeyboardInterface $keyboard
     * @param null $input
     * @return Keyboard
     */
    public function keyboard(KeyboardInterface $keyboard, $input = null): Keyboard
    {
        return $keyboard->create($input);
    }
}
