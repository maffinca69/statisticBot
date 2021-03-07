<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public const CACHE_ACCESS_TOKEN_KEY = 'access_token_';
    public const CACHE_REFRESH_TOKEN_KEY = 'refresh_token_';
    public const CACHE_SPREADSHEET_ID_KEY = 'spreadsheet_id_';

    /**
     * @param    int    $userId
     * @return false|mixed
     */
    public static function getAccessTokenByUserId(int $userId)
    {
        if (!$userId || $userId === 0) {
            return false;
        }

        return Cache::get(self::CACHE_ACCESS_TOKEN_KEY . $userId, '');
    }

    /**
     * @param    int    $userId
     * @return false|mixed
     */
    public static function getRefreshTokenByUserId(int $userId)
    {
        if (!$userId || $userId === 0) {
            return false;
        }

        return Cache::get(self::CACHE_REFRESH_TOKEN_KEY . $userId, '');
    }

    /**
     * @param    int    $userId
     * @return false|mixed
     */
    public static function getSpreadSheetIdByUserId(int $userId)
    {
        if (!$userId || $userId === 0) {
            return false;
        }

        return Cache::get(self::CACHE_SPREADSHEET_ID_KEY . $userId, '');
    }
}
