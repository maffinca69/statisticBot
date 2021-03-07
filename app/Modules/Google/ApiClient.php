<?php
namespace App\Modules\Google;

use App\Parsers\GoogleSpreadSheetParser;
use App\Parsers\ParserInterface;
use App\Services\TokenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiClient
{
    private const API_URL = 'https://sheets.googleapis.com/v4/spreadsheets/';
    private const API_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    /**
     * User spreadsheetID. Unique
     */
    private const SPREADSHEET_ID = '14IN8w3WYjEMAK6jsNa7b8vkWWk-8r72CPuB_XT4rn_s';

    /**
     * @param    null    $sheetName
     * @return string - name of list spreadsheet
     */
    public function fetchSpreadSheet($sheetName = null)
    {
        $params = [
            'includeGridData' => true,
            'ranges' => $sheetName ? "$sheetName!A1:I45" : 'A1:I45',
        ];

        $response = Http::withToken(TokenService::getAccessToken())->get(self::API_URL . self::SPREADSHEET_ID . '?' . http_build_query($params));
        $response = $response->json();
        Log::info($response);

        return $this->parseResponse(new GoogleSpreadSheetParser(), $response);
    }

    public function fetchRefreshToken()
    {
        $clientId = config('google.client_id');
        $clientSecret = config('google.client_secret');
        $grandType = 'refresh_token';
        $refreshToken = config('google.refresh_token');

        $response = Http::post(self::API_TOKEN_URL, [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => $grandType,
            'refresh_token' => $refreshToken,
        ]);

        (new TokenService($this))->saveToken($response->json());
    }

    /**
     * Return formatted value
     *
     * @param    ParserInterface    $parser
     * @param    array    $data
     * @return string
     */
    private function parseResponse(ParserInterface $parser, array $data)
    {
        return $parser->parse($data);
    }
}
