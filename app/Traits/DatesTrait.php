<?php


namespace App\Traits;


use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

trait DatesTrait
{
    /**
     * @param int $subMonths
     * @return array
     */
    public static function getTitleMonth($subMonths = 3): array
    {
        $period = CarbonPeriod::create(Carbon::now()->subMonths($subMonths), Carbon::now());
        $result = [];

        foreach ($period as $index => $date) {
            array_push($result, mb_convert_case(sprintf('%s %s', $date->monthName, $date->year), MB_CASE_TITLE));
        }

        return array_unique($result);
    }
}
