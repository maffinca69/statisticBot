<?php


namespace App\Keyboards;


use Telegram\Bot\Keyboard\Keyboard;

interface KeyboardInterface
{
    public function create($input = null): Keyboard;
}
