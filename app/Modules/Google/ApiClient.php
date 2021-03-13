<?php
namespace App\Modules\Google;

use App\Helpers\CacheHelper;
use App\Parsers\GoogleDriveInfoFileParser;
use App\Parsers\GoogleSpreadSheetParser;
use App\Parsers\ParserInterface;
use App\Services\TokenService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiClient
{
    private const API_URL = 'https://sheets.googleapis.com/v4/spreadsheets/';
    private const DRIVE_API_URL = 'https://www.googleapis.com/drive/v3/files/';
    private const SPREADSHEET_BASE_URL = 'https://docs.google.com/spreadsheets/d/%s/edit#gid=%s';
    private const API_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    public const TOKEN_IS_EXPIRED = '🛠 Token is expired';

    public string $statisticUrl = '';

    /**
     * @param    int    $userId
     * @param    null    $sheetName
     * @return string - name of list spreadsheet
     */
    public function fetchSpreadSheet(int $userId, $sheetName = null)
    {
        // prepare request
        $token = CacheHelper::getAccessTokenByUserId($userId);
        $spreadsheetId = CacheHelper::getSpreadSheetIdByUserId($userId);
        $response = Http::withToken($token)->get(self::API_URL . $spreadsheetId, [
            'includeGridData' => true,
            'ranges' => $sheetName ? "$sheetName!A1:I45" : 'A1:I45',
        ]);

        $response = $response->json();

        if (isset($response['error']) && $response['error']['code'] === Response::HTTP_UNAUTHORIZED) {
            return self::TOKEN_IS_EXPIRED;
        }

        Log::info($response);

        $text = self::parseResponse(new GoogleSpreadSheetParser(), $response);

        if (!empty($text)) {
            $text .= $this->loadAdditionallyInfo($userId, $response);
        }

        return $text;
    }

    /**
     * @param    int    $userId
     * @param    array    $generalResponse
     * @return string
     */
    private function loadAdditionallyInfo(int $userId, array $generalResponse): string
    {
        $this->statisticUrl = self::buildStatisticUrl($generalResponse);
        $info = $this->fetchInfoFile($userId, $generalResponse['spreadsheetId']);

        if (isset($info['error'])) {
            return '';
        }

        $parsedInfo = self::parseResponse(new GoogleDriveInfoFileParser(), $info);
        return PHP_EOL . PHP_EOL . $parsedInfo;
    }

    /**
     * Build current spreadsheet link
     *
     * @param    array    $data - google spreadsheet response
     * @return string
     */
    private static function buildStatisticUrl(array $data): string
    {
        $sheetId = current($data['sheets'])['properties']['sheetId'];
        $spreadsheetId = $data['spreadsheetId'];

        return sprintf(self::SPREADSHEET_BASE_URL,$spreadsheetId, $sheetId);
    }

    /**
     * Fetch refresh token vy userID
     *
     * @param $userId
     */
    public function fetchRefreshToken($userId)
    {
        $clientId = config('google.client_id');
        $clientSecret = config('google.client_secret');
        $grandType = 'refresh_token';
        $refreshToken = CacheHelper::getRefreshTokenByUserId($userId);

        $response = Http::post(self::API_TOKEN_URL, [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => $grandType,
            'refresh_token' => $refreshToken,
        ]);

        (new TokenService($this))->saveToken($response->json(), $userId);
    }

    /**
     * @param    int    $userId
     * @param    $fileId
     * @return array|mixed
     */
    public function fetchInfoFile(int $userId, $fileId)
    {
        $response = Http::withToken(CacheHelper::getAccessTokenByUserId($userId))
            ->get(self::DRIVE_API_URL . $fileId, [
                'fields' => 'modifiedTime'
            ]);

        return $response->json();
    }

    /**
     * Return formatted value
     *
     * @param    ParserInterface    $parser
     * @param    array    $data
     * @return string
     */
    private static function parseResponse(ParserInterface $parser, array $data)
    {
        return $parser->parse($data);
    }
}
