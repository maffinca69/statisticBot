<?php


namespace App\Traits;


use App\Helpers\CacheHelper;

trait AuthTrait
{
    protected int $userId;

    /**
     * @return bool
     */
    public function isAuthRequired(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAuth(): bool
    {
        $update = $this->telegram->getWebhookUpdate();

        $this->userId = $update->isType('callback_query') ?
            $update->callbackQuery->from->id :
            $update->message->from->id;

        return CacheHelper::getAccessTokenByUserId($this->userId) && CacheHelper::getSpreadSheetIdByUserId($this->userId);
    }
}
