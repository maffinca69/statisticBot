<?php
namespace App\Modules\Google;

use App\Services\TokenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiClient
{
    private const API_URL = 'https://sheets.googleapis.com/v4/spreadsheets/';
    private const API_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const SPREADSHEET_ID = '14IN8w3WYjEMAK6jsNa7b8vkWWk-8r72CPuB_XT4rn_s';

    public function fetchSpreadSheet($sheetName = null)
    {
        $params = [
            'includeGridData' => true,
            'ranges' => $sheetName ? "$sheetName!A1:I45" : 'A1:I45',
        ];

        $response = Http::withToken(TokenService::getAccessToken())->get(self::API_URL . self::SPREADSHEET_ID . '?' . http_build_query($params));
        $response = $response->json();
        Log::info($response);

        $sheet = current($response['sheets']);

        $title = $sheet['properties']['title'] . PHP_EOL . PHP_EOL;

        $positionStatus = current($sheet['data'])['rowData'][2]['values'][1]['formattedValue'];
        $position = '🧑‍💻 ' . current($sheet['data'])['rowData'][1]['values'][1]['formattedValue'] . ' (' . $positionStatus . ')' . PHP_EOL;

        $trackedValue = current($sheet['data'])['rowData'][9]['values'][1]['formattedValue'];
        $trackedType = plural_form($trackedValue, ['час', 'часа', 'часов']);
        $tracked = '⏱ ' . $trackedValue . ' ' . $trackedType . PHP_EOL;

        $salary = '💸 ' . current($sheet['data'])['rowData'][3]['values'][8]['formattedValue'] . PHP_EOL;

        $text = $title . $position . $tracked . $salary;

        // Statistic
        $text .= PHP_EOL;

        // План/оценка
        $planEstimate = current($sheet['data'])['rowData'][40]['values'];
        $planEstimateText = $planEstimate[0]['formattedValue'];
        $planEstimateValue = $planEstimate[1]['formattedValue'];
        $planEstimate = str_replace('- ', '', $planEstimateText) . ': ' . $planEstimateValue . PHP_EOL;

        // Оценка/трудозатраты
        $estimateTimeEntries = current($sheet['data'])['rowData'][41]['values'];
        $estimateTimeEntriesText = $estimateTimeEntries[0]['formattedValue'];
        $estimateTimeEntriesValue = $estimateTimeEntries[1]['formattedValue'];
        $estimateTimeEntries = str_replace('- ', '', $estimateTimeEntriesText) . ': ' . $estimateTimeEntriesValue . PHP_EOL;

        // План/трудозатраты
        $planTimeEntries = current($sheet['data'])['rowData'][42]['values'];
        $planTimeEntriesText = $planTimeEntries[0]['formattedValue'];
        $planTimeEntriesValue = $planTimeEntries[1]['formattedValue'];
        $planTimeEntries = str_replace('- ', '', $planTimeEntriesText) . ': ' . $planTimeEntriesValue . PHP_EOL;

        // Процент выработки
        $percent = current($sheet['data'])['rowData'][43]['values'];
        $percentText = $percent[0]['formattedValue'];
        $percentValue = $percent[1]['formattedValue'];
        $percent = str_replace('- ', '', $percentText) . ': ' . $percentValue . PHP_EOL;

        $text .= $planEstimate . $estimateTimeEntries . $planTimeEntries . $percent;

        return $text;
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
}
