<?php


namespace App\Helpers;


use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;

class KeyboardHelper
{
    public const STATISTIC_LINK_TEXT = 'Ссылка на расчетку';
    public const LOGOUT_CONFIRM_TEXT = 'Подтвердить';

    /**
     * @param    string    $url
     * @return InlineKeyboard
     */
    public static function buildInlineKeyboardLinkButton(string $url): InlineKeyboard
    {
        return new InlineKeyboard([
            new InlineKeyboardButton([
                'text' => self::STATISTIC_LINK_TEXT,
                'url' => $url
            ])
        ]);
    }

    /**
     * @return InlineKeyboard
     */
    public static function buildSelectMonthInlineKeyboard(): InlineKeyboard
    {
        $rows = [];
        $period = CarbonPeriod::create(Carbon::now()->subMonths(3), Carbon::now());

        $lastMonth = '';
        foreach ($period as $index => $date) {
            if ($date->monthName === $lastMonth) {
                continue;
            }

            $data = mb_convert_case(sprintf('%s %s', $date->monthName, $date->year), MB_CASE_TITLE);
            array_push($rows, ['text' => $data, 'callback_data' => $data]);

            $lastMonth = $date->monthName;
        }

        $rows = array_chunk(array_reverse($rows), 2);

        return new InlineKeyboard($rows[0], $rows[1]);
    }

    public static function inlineLogoutKeyboard(): InlineKeyboard
    {
        return new InlineKeyboard([
            new InlineKeyboardButton([
                'text' => self::LOGOUT_CONFIRM_TEXT,
                'callback_data' => 'logout'
           ])
        ]);
    }
}
