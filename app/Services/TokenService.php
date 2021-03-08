<?php


namespace App\Services;


use App\Helpers\CacheHelper;
use App\Jobs\RefreshTokenJob;
use App\Modules\Google\ApiClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TokenService
{
    public const CACHE_ACCESS_TOKEN_KEY = 'access_token';

    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Save access token after refreshing
     *
     * @param    array    $data
     * @param    int    $userId - user which was authenticated
     * @return bool
     */
    public function saveToken(array $data, int $userId)
    {
        $savedAccess = Cache::put(CacheHelper::CACHE_ACCESS_TOKEN_KEY  . $userId, $data['access_token']);
        $savedRefresh = Cache::put(CacheHelper::CACHE_REFRESH_TOKEN_KEY . $userId, $data['refresh_token']);

        if ($savedAccess && $savedRefresh) {
            Log::info('Tokens saved. Next refreshing - ' . Carbon::now()->addSeconds($data['expires_in'])->format('d.m.y H:m:s'));
            $this->scheduleRefreshToken($data['expires_in'], $userId);
            return true;
        }

        return false;
    }

    /**
     * Fetch refresh token
     * @param $userId
     */
    public function refreshToken($userId)
    {
        $this->client->fetchRefreshToken($userId);
    }

    /**
     * Adding to queue
     *
     * @param    int    $expire
     * @param $userId
     */
    public function scheduleRefreshToken(int $expire, $userId)
    {
        dispatch((new RefreshTokenJob($this, $userId))->delay($expire));
        Log::info('Token was refreshing ' . $userId . ' - ' .
                  Carbon::now()->addSeconds($expire)->format('d.m.y H:m:s'));
    }
}
