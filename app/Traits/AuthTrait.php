<?php


namespace App\Traits;


use App\Helpers\CacheHelper;

trait AuthTrait
{
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
        $userId = $this->telegram->getUserId();

        return CacheHelper::getAccessTokenByUserId($userId) && CacheHelper::getSpreadSheetIdByUserId($userId);
    }
}
