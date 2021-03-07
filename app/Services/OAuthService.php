<?php


namespace App\Services;


use App\Helpers\CacheHelper;
use Google_Client;
use Google_Service_Sheets;
use Illuminate\Support\Facades\Cache;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class OAuthService
{
    private Google_Client $googleClient;
    private TokenService $tokenService;

    public function __construct(Google_Client $googleClient, TokenService $tokenService)
    {
        $this->googleClient = $googleClient;
        $this->googleClient->setAuthConfig(storage_path('app/client_secret.json'));
        $this->googleClient->addScope(Google_Service_Sheets::SPREADSHEETS);
        $this->googleClient->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $this->googleClient->setAccessType('offline');
        $this->googleClient->setPrompt('consent');
        $this->googleClient->setIncludeGrantedScopes(true);   // incremental auth

        $this->tokenService = $tokenService;
    }

    public function auth(int $userId): ServerResponse
    {
        $this->googleClient->setState((string)$userId);
        $auth_url = $this->googleClient->createAuthUrl();

        return Request::sendMessage([
            'chat_id' => $userId,
            'text' => 'Необходимо авторизоваться по ссылке ниже:' . PHP_EOL .  filter_var($auth_url, FILTER_SANITIZE_URL)
        ]);
    }

    public function authByCode(array $data): bool
    {
        $this->googleClient->authenticate($data['code']);
        $tokenInfo = $this->googleClient->getAccessToken();
        $userId = intval($data['state']);

        if ($this->tokenService->saveToken($tokenInfo, $userId)) {
            // maybe refactoring...
            Request::sendMessage([
                'chat_id' => $userId,
                'text' => '🎉 Успешно авторизованы!' . PHP_EOL . 'Теперь отправьте ссылку на свою расчетку'
            ]);

            return true;
        }

        return true;
    }

    public function saveSpreadSheetId(int $userId, string $spreadsheetId)
    {
        return Cache::put(CacheHelper::CACHE_SPREADSHEET_ID_KEY . $userId, $spreadsheetId);
    }
}
