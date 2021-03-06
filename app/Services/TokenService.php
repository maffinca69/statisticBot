<?php


namespace App\Services;


use App\Jobs\RefreshTokenJob;
use App\Modules\Google\ApiClient;
use Illuminate\Support\Facades\Cache;

class TokenService
{
    public const CACHE_ACCESS_TOKEN_KEY = 'access_token';

    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function saveToken(array $data)
    {
        $savedAccess = Cache::put(self::CACHE_ACCESS_TOKEN_KEY, $data['access_token']);

        if ($savedAccess) {
            $this->scheduleRefreshToken($data['expires_in']);
            return true;
        }

        return false;
    }

    public function refreshToken()
    {
        $this->client->fetchRefreshToken();
    }

    public function scheduleRefreshToken(int $expire)
    {
        dispatch((new RefreshTokenJob($this))->delay($expire));
    }

    public static function getAccessToken()
    {
        return Cache::get(self::CACHE_ACCESS_TOKEN_KEY, '');
    }
}
