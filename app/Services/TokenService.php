<?php


namespace App\Services;


use App\Helpers\CacheHelper;
use App\Jobs\RefreshTokenJob;
use App\Modules\Google\ApiClient;
use Illuminate\Support\Facades\Cache;

class TokenService
{
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

        $savedRefresh = true;
        if (isset($data['refresh_token'])) {
            $savedRefresh = Cache::put(CacheHelper::CACHE_REFRESH_TOKEN_KEY . $userId, $data['refresh_token']);
        }

        if ($savedAccess && $savedRefresh) {
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
    }
}
