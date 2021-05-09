<?php


namespace App\Services;


use App\Helpers\CacheHelper;
use App\Modules\Telegram\Api;
use Google\Exception;
use Google_Client;
use Google_Service_Sheets;
use Illuminate\Support\Facades\Cache;

use Telegram;

class OAuthService
{
    private Google_Client $googleClient;
    private TokenService $tokenService;

    public function __construct(Google_Client $googleClient, TokenService $tokenService)
    {
        $this->googleClient = $googleClient;
        $this->initGoogleAuthClient();

        $this->tokenService = $tokenService;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function initGoogleAuthClient(): void
    {
        $this->googleClient->setAuthConfig(storage_path('app/client_secret.json'));
        $this->googleClient->addScope(Google_Service_Sheets::SPREADSHEETS);
        $this->googleClient->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $this->googleClient->setAccessType('offline');
        $this->googleClient->setPrompt('consent');
        $this->googleClient->setIncludeGrantedScopes(true);   // incremental auth
    }

    public function auth(int $userId): string
    {
        $this->googleClient->setState((string)$userId);
        $url =  $this->googleClient->createAuthUrl();

        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * @param array $data
     * @return false|int
     */
    public function authByCode(array $data)
    {
        $this->googleClient->fetchAccessTokenWithAuthCode($data['code']);
        $tokenInfo = $this->googleClient->getAccessToken() ?? [];
        $userId = intval($data['state']);

        if ($this->tokenService->saveToken($tokenInfo, $userId)) {
            return $userId;
        }

        return false;
    }

    /**
     * Logout user
     *
     * @param    int    $userId
     * @return bool
     */
    public function logout(int $userId): bool
    {
        $revokeAccess = CacheHelper::revokeAccessToken($userId);
        $revokeRefresh = CacheHelper::revokeRefreshToken($userId);
        $revokeSpreadSheet = CacheHelper::revokeSpreadSheetId($userId);

        return $revokeAccess && $revokeRefresh && $revokeSpreadSheet;
    }

    /**
     * Save user spreadsheetID
     *
     * @param    int    $userId
     * @param    string    $spreadsheetId
     * @return bool
     */
    public function saveSpreadSheetId(int $userId, string $spreadsheetId): bool
    {
        return Cache::put(CacheHelper::CACHE_SPREADSHEET_ID_KEY . $userId, $spreadsheetId);
    }
}
