<?php
namespace App\Modules\Google;

use App\Helpers\CacheHelper;
use App\Parsers\GoogleSpreadSheetParser;
use App\Parsers\ParserInterface;
use App\Services\TokenService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class ApiClient
{
    private const API_URL = 'https://sheets.googleapis.com/v4/spreadsheets/';
    private const SPREADSHEET_BASE_URL = 'https://docs.google.com/spreadsheets/d/%s/edit#gid=%s';
    private const API_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    /**
     * @param    int    $userid
     * @param    null    $sheetName
     * @return string - name of list spreadsheet
     */
    public function fetchSpreadSheet(int $userid, $sheetName = null)
    {
        $params = [
            'includeGridData' => true,
            'ranges' => $sheetName ? "$sheetName!A1:I45" : 'A1:I45',
        ];

        $response = Http::withToken(CacheHelper::getAccessTokenByUserId($userid))
            ->get(self::API_URL . CacheHelper::getSpreadSheetIdByUserId($userid) . '?' . http_build_query($params));
        $response = $response->json();

        if (isset($response['error']) && $response['error']['code'] === Response::HTTP_UNAUTHORIZED) {
            return '🛠 Token is expired';
        }

        $text = self::parseResponse(new GoogleSpreadSheetParser(), $response);

        if (!empty($text)) {
            $link = self::buildStatisticUrl($response);
            $linkText = 'Ссылка на расчетку';
            $text .= PHP_EOL . PHP_EOL . self::buildHyperlink($link, $linkText);
        }

        return $text;
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
     * @param    string    $link
     * @param    string    $text
     * @return string
     */
    private static function buildHyperlink(string $link, string $text): string
    {
        return sprintf('[%s](%s)', $text, $link);
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
