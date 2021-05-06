<?php


namespace App\Modules\Redmine\Api;


use App\Modules\Redmine\ApiRequest;

class UserInfo extends ApiRequest
{
    public function __construct(int $telegramUserId, string $method = '')
    {
        parent::__construct($telegramUserId, 'my.account');
    }
}
