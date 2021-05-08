<?php


namespace App\Helpers;


use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Telegram\Bot\Keyboard\Keyboard;

class KeyboardHelper
{
    public const STATISTIC_LINK_TEXT = 'Ссылка на расчетку';
    public const ACTUAL_STATISTIC_TEXT = 'Актуальная расчетка';
    public const OTHER_MONTH_STATISTIC_TEXT = 'Выбрать месяц';

    public const LOGOUT_CONFIRM_TEXT = 'Подтвердить';

    /**
     * @param string $url
     * @return InlineKeyboard
     */
    public static function inlineKeyboardLinkButton(string $url): InlineKeyboard
    {
        return [
            [
                Keyboard::inlineButton(['text' => self::STATISTIC_LINK_TEXT, 'url' => $url]),
                Keyboard::inlineButton(['text' => self::ACTUAL_STATISTIC_TEXT, 'callback_data' => 'request']),
                Keyboard::inlineButton(['text' => self::OTHER_MONTH_STATISTIC_TEXT, 'callback_data' => 'select'])
            ]
        ];
    }

    /**
     * @return InlineKeyboard
     */
    public static function inlineKeyboardSelectMonth(): InlineKeyboard
    {
        $rows = [];

        $months = self::getMonth();
        foreach ($months as $month) {
            array_push($rows, ['text' => $month, 'callback_data' => $month]);
        }

        $rows = array_chunk(array_reverse($rows), 2);

        return new InlineKeyboard($rows[0], $rows[1]);
    }

    public static function getMonth($subMonths = 3): array
    {
        $period = CarbonPeriod::create(Carbon::now()->subMonths($subMonths), Carbon::now());

        $lastMonth = '';
        $result = [];

        foreach ($period as $index => $date) {
            if ($date->monthName === $lastMonth) {
                continue;
            }

            array_push($result, mb_convert_case(sprintf('%s %s', $date->monthName, $date->year), MB_CASE_TITLE));

            $lastMonth = $date->monthName;
        }

        return $result;
    }
}
