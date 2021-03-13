<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public const CACHE_ACCESS_TOKEN_KEY = 'access_token_';
    public const CACHE_REFRESH_TOKEN_KEY = 'refresh_token_';
    public const CACHE_SPREADSHEET_ID_KEY = 'spreadsheet_id_';

    /**
     * Get access token by user id
     *
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
     * Get refresh token by userID
     *
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
     * Get spreadsheetID by userID
     *
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

    /**
     * Get all ids user which logged
     */
    public static function getAllIdsUsersFromCache(): array
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

    /**
     * @param    int    $userId
     * @return bool
     */
    public static function revokeAccessToken(int $userId)
    {
        return Cache::forget(CacheHelper::CACHE_ACCESS_TOKEN_KEY . $userId);
    }

    /**
     * @param    int    $userId
     * @return bool
     */
    public static function revokeRefreshToken(int $userId)
    {
        return Cache::forget(CacheHelper::CACHE_REFRESH_TOKEN_KEY . $userId);
    }

    /**
     * @param    int    $userId
     * @return bool
     */
    public static function revokeSpreadSheetId(int $userId)
    {
        return Cache::forget(CacheHelper::CACHE_SPREADSHEET_ID_KEY . $userId);
    }
}
